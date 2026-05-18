<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingCategory;
use App\Models\AccountingPeriod;
use App\Models\AccountingSetting;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    private function getDapurId(): int
    {
        return Auth::user()->userRole->id_dapur;
    }

    // ─── Settings (Identitas Lembaga) ───

    public function index()
    {
        $dapurId = $this->getDapurId();
        $dapur        = Auth::user()->userRole->dapur;
        $setting      = AccountingSetting::firstOrCreate(['id_dapur' => $dapurId]);
        $periods      = AccountingPeriod::forDapur($dapurId)->orderByDesc('end_date')->get();
        $categories   = AccountingCategory::forDapur($dapurId)->orderBy('group')->orderBy('name')->get();
        $cashAccounts = CashAccount::forDapur($dapurId)->orderBy('name')->get();

        // ── Periode baru: hitung saldo awal yang disarankan dari periode closed terakhir ──
        $openPeriod   = $periods->firstWhere('status', 'open');
        $canCreateNewPeriod = $openPeriod === null;

        // Periode closed paling baru (sebagai sumber carry-over)
        $lastClosedPeriod = $periods->where('status', 'closed')->first(); // already ordered desc

        // suggested opening balances per cash_account_id
        $suggestedOpeningBalances = [];
        if ($lastClosedPeriod) {
            foreach ($lastClosedPeriod->balances as $bal) {
                // closing_balance bisa null jika periode lama belum di-compute; fallback ke 0
                $suggestedOpeningBalances[$bal->cash_account_id] = (float) ($bal->closing_balance ?? 0);
            }
        }

        return view('akuntan.pengaturan.index', compact(
            'dapur', 'setting', 'periods', 'categories', 'cashAccounts',
            'canCreateNewPeriod', 'lastClosedPeriod', 'suggestedOpeningBalances', 'openPeriod'
        ));
    }

    public function updateSettings(Request $request)
    {
        $dapurId = $this->getDapurId();
        $validated = $request->validate([
            'institution_name' => 'nullable|string|max:255',
            'address'          => 'nullable|string',
            'head_name'        => 'nullable|string|max:255',
            'treasurer_name'   => 'nullable|string|max:255',
            'foundation_name'  => 'nullable|string|max:255',
            'foundation_head'  => 'nullable|string|max:255',
            'bank_account'     => 'nullable|string|max:100',
            'report_location'  => 'nullable|string|max:255',
            'report_date'      => 'nullable|date',
        ]);

        AccountingSetting::updateOrCreate(['id_dapur' => $dapurId], $validated);
        return back()->with('success', 'Data identitas lembaga berhasil disimpan.');
    }

    // ─── Periode ───

    public function storePeriode(Request $request)
    {
        $dapurId = $this->getDapurId();

        // Aturan 1: tidak boleh ada periode OPEN terlebih dahulu
        $existingOpen = AccountingPeriod::forDapur($dapurId)->where('status', 'open')->first();
        if ($existingOpen) {
            return back()
                ->withErrors(['error' => 'Tidak dapat membuat periode baru. Periode "' . $existingOpen->name . '" masih berstatus Buka Transaksi. Tutup periode tersebut terlebih dahulu.'])
                ->withInput();
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'year'       => 'required|integer|min:2020|max:2099',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $openingBalances = $request->input('opening_balances', []);
        $cashAccounts    = CashAccount::forDapur($dapurId)->get();

        // Validasi: opening balance tidak boleh kurang dari saldo akhir periode sebelumnya
        $lastClosedPeriod = AccountingPeriod::latestClosed($dapurId)->first();
        if ($lastClosedPeriod) {
            foreach ($cashAccounts as $ca) {
                $amount = isset($openingBalances[$ca->id]) ? (float) $openingBalances[$ca->id] : 0;
                $prevBal = $lastClosedPeriod->balances->where('cash_account_id', $ca->id)->first();
                $prevClosing = $prevBal ? (float) $prevBal->closing_balance : 0;
                
                if ($amount < $prevClosing) {
                    return back()
                        ->withErrors(['error' => "Saldo awal untuk {$ca->name} (Rp " . number_format($amount, 0, ',', '.') . ") tidak boleh kurang dari saldo akhir bawaan periode sebelumnya (Rp " . number_format($prevClosing, 0, ',', '.') . ")."])
                        ->withInput();
                }
            }
        }

        $period = AccountingPeriod::create([
            'id_dapur'   => $dapurId,
            'name'       => $validated['name'],
            'year'       => $validated['year'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'status'     => 'open',
        ]);

        // Simpan opening_balance per akun kas
        foreach ($cashAccounts as $ca) {
            $amount = isset($openingBalances[$ca->id]) ? (float) $openingBalances[$ca->id] : 0;
            AccountingBalance::create([
                'period_id'       => $period->id,
                'cash_account_id' => $ca->id,
                'opening_balance' => $amount,
            ]);
        }

        return back()->with('success', 'Periode "' . $period->name . '" berhasil dibuat.');
    }

    public function updatePeriode(Request $request, AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'year'       => 'required|integer|min:2020|max:2099',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // Validasi: opening balance tidak boleh kurang dari saldo akhir bawaan periode sebelumnya
        if ($periode->isOpen()) {
            $balancesInput = $request->input('opening_balances', []);
            $prevPeriod = $periode->getPreviousPeriod();
            
            if ($prevPeriod) {
                $cashAccounts = CashAccount::forDapur($dapurId)->get();
                foreach ($cashAccounts as $ca) {
                    $amount = isset($balancesInput[$ca->id]) ? (float) $balancesInput[$ca->id] : 0;
                    $prevBal = $prevPeriod->balances->where('cash_account_id', $ca->id)->first();
                    $prevClosing = $prevBal ? (float) $prevBal->closing_balance : 0;
                    if ($amount < $prevClosing) {
                        return back()
                            ->withErrors(['error' => "Saldo awal untuk {$ca->name} (Rp " . number_format($amount, 0, ',', '.') . ") tidak boleh kurang dari saldo akhir bawaan periode sebelumnya (Rp " . number_format($prevClosing, 0, ',', '.') . ")."]);
                    }
                }
            }
        }

        $periode->update([
            'name'       => $validated['name'],
            'year'       => $validated['year'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
        ]);

        // Update opening balances (hanya untuk periode yang masih open – periode closed tidak boleh diubah)
        if ($periode->isOpen()) {
            $balances = $request->input('opening_balances', []);
            foreach ($balances as $cashAccountId => $amount) {
                AccountingBalance::updateOrCreate(
                    ['period_id' => $periode->id, 'cash_account_id' => $cashAccountId],
                    ['opening_balance' => $amount ?? 0]
                );
            }
        }

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    /**
     * Menutup periode: hitung closing_balance tiap akun kas lalu ubah status.
     */
    public function closePeriode(AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);

        if ($periode->isClosed()) {
            return back()->withErrors(['error' => 'Periode sudah ditutup.']);
        }

        // Hitung dan simpan closing_balance per akun kas
        $closingBalances = $periode->computeAndSaveClosingBalances();

        // Ubah status menjadi closed
        $periode->update(['status' => 'closed']);

        $totalClosing = array_sum($closingBalances);

        return back()->with('success',
            'Periode "' . $periode->name . '" berhasil ditutup. '
            . 'Total saldo akhir: Rp ' . number_format($totalClosing, 0, ',', '.')
        );
    }

    public function openPeriode(AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);

        // Tidak bisa buka kembali jika ada periode lain yang sudah dibuat setelahnya
        $newerPeriod = AccountingPeriod::forDapur($dapurId)
            ->where('start_date', '>', $periode->end_date)
            ->exists();

        if ($newerPeriod) {
            return back()->withErrors([
                'error' => 'Periode tidak dapat dibuka kembali karena sudah ada periode penerus. Hapus periode penerus terlebih dahulu.'
            ]);
        }

        // Hapus closing_balance agar saldo bersih kembali terhitung dari transaksi
        foreach ($periode->balances as $bal) {
            $bal->update(['closing_balance' => null]);
        }

        $periode->update(['status' => 'open']);
        return back()->with('success', 'Periode "' . $periode->name . '" berhasil dibuka kembali. Saldo akhir di-reset.');
    }

    public function destroyPeriode(AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);
        if ($periode->transactions()->count() > 0) {
            return back()->withErrors(['error' => 'Periode tidak dapat dihapus karena masih memiliki transaksi.']);
        }
        $periode->delete();
        return back()->with('success', 'Periode berhasil dihapus.');
    }

    // ─── Kategori ───

    public function storeKategori(Request $request)
    {
        $dapurId = $this->getDapurId();
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'type'   => 'required|in:income,expense',
            'group'  => 'required|in:dana_bahan_baku,dana_operasional,dana_insentif_fasilitas,pungutan_ppn,pungutan_pph21,pungutan_pph22,pungutan_pph23,pungutan_pph4,biaya_bahan_baku,biaya_operasional,biaya_insentif_fasilitas',
            'is_tax' => 'boolean',
        ]);
        $validated['id_dapur'] = $dapurId;
        $validated['is_tax'] = $request->boolean('is_tax');
        AccountingCategory::create($validated);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, AccountingCategory $kategori)
    {
        $dapurId = $this->getDapurId();
        if ($kategori->id_dapur && $kategori->id_dapur != $dapurId) abort(403);

        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'type'   => 'required|in:income,expense',
            'group'  => 'required|in:dana_bahan_baku,dana_operasional,dana_insentif_fasilitas,pungutan_ppn,pungutan_pph21,pungutan_pph22,pungutan_pph23,pungutan_pph4,biaya_bahan_baku,biaya_operasional,biaya_insentif_fasilitas',
            'is_tax' => 'boolean',
        ]);
        $validated['is_tax'] = $request->boolean('is_tax');
        $kategori->update($validated);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(AccountingCategory $kategori)
    {
        $dapurId = $this->getDapurId();
        if ($kategori->id_dapur && $kategori->id_dapur != $dapurId) abort(403);
        if ($kategori->is_protected) {
            return back()->withErrors(['error' => 'Kategori bawaan sistem tidak dapat dihapus.']);
        }
        if ($kategori->transactions()->count() > 0) {
            return back()->withErrors(['error' => 'Kategori tidak dapat dihapus karena masih digunakan transaksi.']);
        }
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // ─── Akun Kas ───

    public function storeKas(Request $request)
    {
        $dapurId = $this->getDapurId();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:tunai,bank',
        ]);
        $validated['id_dapur'] = $dapurId;
        $cashAccount = CashAccount::create($validated);

        // Add opening balance for existing open periods
        $openPeriods = AccountingPeriod::forDapur($dapurId)->where('status', 'open')->get();
        foreach ($openPeriods as $period) {
            AccountingBalance::firstOrCreate(
                ['period_id' => $period->id, 'cash_account_id' => $cashAccount->id],
                ['opening_balance' => $request->input('opening_balance', 0)]
            );
        }
        return back()->with('success', 'Akun kas berhasil ditambahkan.');
    }

    public function updateKas(Request $request, CashAccount $kas)
    {
        $dapurId = $this->getDapurId();
        if ($kas->id_dapur != $dapurId) abort(403);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:tunai,bank',
        ]);
        $kas->update($validated);
        return back()->with('success', 'Akun kas berhasil diperbarui.');
    }

    public function destroyKas(CashAccount $kas)
    {
        $dapurId = $this->getDapurId();
        if ($kas->id_dapur != $dapurId) abort(403);
        if ($kas->transactions()->count() > 0) {
            return back()->withErrors(['error' => 'Akun kas tidak dapat dihapus karena masih digunakan transaksi.']);
        }
        $kas->delete();
        return back()->with('success', 'Akun kas berhasil dihapus.');
    }
}
