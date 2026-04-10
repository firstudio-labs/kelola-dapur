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
        $setting  = AccountingSetting::firstOrCreate(['id_dapur' => $dapurId]);
        $periods  = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();
        $categories = AccountingCategory::forDapur($dapurId)->orderBy('group')->orderBy('name')->get();
        $cashAccounts = CashAccount::forDapur($dapurId)->orderBy('name')->get();

        return view('akuntan.pengaturan.index', compact('setting', 'periods', 'categories', 'cashAccounts'));
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
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'year'             => 'required|integer|min:2020|max:2099',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'opening_balance'  => 'required|numeric|min:0',
        ]);

        $period = AccountingPeriod::create([
            'id_dapur'   => $dapurId,
            'name'       => $validated['name'],
            'year'       => $validated['year'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'status'     => 'open',
        ]);

        $balances = $request->input('opening_balances', []);
        foreach ($balances as $cashAccountId => $amount) {
            AccountingBalance::create([
                'period_id'       => $period->id,
                'cash_account_id' => $cashAccountId,
                'opening_balance' => $amount ?? 0,
            ]);
        }

        return back()->with('success', 'Periode berhasil dibuat.');
    }

    public function updatePeriode(Request $request, AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);

        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'year'             => 'required|integer|min:2020|max:2099',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'opening_balance'  => 'required|numeric|min:0',
        ]);

        $periode->update([
            'name'       => $validated['name'],
            'year'       => $validated['year'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
        ]);

        $balances = $request->input('opening_balances', []);
        foreach ($balances as $cashAccountId => $amount) {
            AccountingBalance::updateOrCreate(
                ['period_id' => $periode->id, 'cash_account_id' => $cashAccountId],
                ['opening_balance' => $amount ?? 0]
            );
        }

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function closePeriode(AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);
        $periode->update(['status' => 'closed']);
        return back()->with('success', 'Periode berhasil ditutup.');
    }

    public function openPeriode(AccountingPeriod $periode)
    {
        $dapurId = $this->getDapurId();
        if ($periode->id_dapur != $dapurId) abort(403);
        $periode->update(['status' => 'open']);
        return back()->with('success', 'Periode berhasil dibuka kembali.');
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
