<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingCategory;
use App\Models\AccountingPeriod;
use App\Models\AccountingTransaction;
use App\Models\CashAccount;
use App\Models\TransaksiDapur;
use App\Models\AccountingTransactionShortage;
use App\Models\LaporanKekuranganStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    private function getDapurId(): int
    {
        return Auth::user()->userRole->id_dapur;
    }

    public function index(Request $request)
    {
        $dapurId = $this->getDapurId();
        
        $years = AccountingPeriod::forDapur($dapurId)
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $selectedYear = $request->get('year', $years->first() ?? date('Y'));
        
        $periods = AccountingPeriod::forDapur($dapurId)
            ->where('year', $selectedYear)
            ->orderByDesc('start_date')
            ->get();

        $categories = AccountingCategory::forDapur($dapurId)->orderBy('name')->get();

        $selectedPeriodId = $request->get('period_id');
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());

        if ($activePeriod && $activePeriod->year != $selectedYear) {
            $activePeriod = $periods->first();
        }

        $query = AccountingTransaction::with(['category', 'cashAccount', 'creator'])
            ->forDapur($dapurId)
            ->orderedByDate();

        if ($activePeriod) {
            $query->forPeriod($activePeriod->id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('description', 'like', "%{$q}%")
                   ->orWhere('no_bukti', 'like', "%{$q}%");
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('akuntan.transaksi.index', compact(
            'transactions', 'periods', 'categories', 'activePeriod', 'years', 'selectedYear'
        ));
    }

    public function create()
    {
        $dapurId    = $this->getDapurId();
        $periods    = AccountingPeriod::forDapur($dapurId)->where('status', 'open')->orderByDesc('year')->get();
        $categories = AccountingCategory::forDapur($dapurId)->orderBy('name')->get();
        $cashAccounts = CashAccount::forDapur($dapurId)->orderBy('name')->get();

        return view('akuntan.transaksi.create', compact('periods', 'categories', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $dapurId = $this->getDapurId();

        $validated = $request->validate([
            'period_id'       => 'required|exists:accounting_periods,id',
            'date'            => 'required|date',
            'no_bukti'        => 'nullable|string|max:50',
            'description'     => 'required|string|max:255',
            'category_id'     => 'required|exists:accounting_categories,id',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'debit'           => 'nullable|numeric|min:0',
            'credit'          => 'nullable|numeric|min:0',
        ]);

        $debit  = (float) ($validated['debit']  ?? 0);
        $credit = (float) ($validated['credit'] ?? 0);

        // Validation rules
        if ($debit > 0 && $credit > 0) {
            throw ValidationException::withMessages([
                'debit' => 'Debit dan kredit tidak boleh diisi bersamaan.',
            ]);
        }
        if ($debit <= 0 && $credit <= 0) {
            throw ValidationException::withMessages([
                'debit' => 'Salah satu dari debit atau kredit harus lebih besar dari nol.',
            ]);
        }

        $period = AccountingPeriod::findOrFail($validated['period_id']);
        if ($period->id_dapur != $dapurId) {
            abort(403);
        }
        if (!$period->isOpen()) {
            return back()->withErrors(['period_id' => 'Transaksi tidak dapat ditambahkan pada periode yang sudah ditutup.'])->withInput();
        }
        $date = \Carbon\Carbon::parse($validated['date']);
        if ($date->lt($period->start_date) || $date->gt($period->end_date)) {
            throw ValidationException::withMessages([
                'date' => "Tanggal harus berada dalam rentang periode ({$period->start_date->format('d/m/Y')} - {$period->end_date->format('d/m/Y')}).",
            ]);
        }

        $transaction = AccountingTransaction::create([
            'period_id'       => $period->id,
            'date'            => $validated['date'],
            'month'           => $date->month,
            'no_bukti'        => $validated['no_bukti'],
            'description'     => $validated['description'],
            'category_id'     => $validated['category_id'],
            'cash_account_id' => $validated['cash_account_id'],
            'debit'           => $debit,
            'credit'          => $credit,
            'created_by'      => Auth::id(),
        ]);

        if ($request->filled('shortages_json')) {
            $shortagesData = json_decode($request->input('shortages_json'), true);
            \Log::info('Shortages JSON received:', ['data' => $shortagesData]);
            if (is_array($shortagesData) && count($shortagesData) > 0) {
                foreach ($shortagesData as $shortage) {
                    if (!empty($shortage['laporan_id']) && isset($shortage['qty']) && isset($shortage['nominal'])) {
                        $transaction->shortages()->create([
                            'laporan_kekurangan_id' => $shortage['laporan_id'],
                            'harga_satuan'          => $shortage['harga_satuan'] ?? 0,
                            'qty_dibeli'            => $shortage['qty'],
                            'nominal'               => $shortage['nominal'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('akuntan.transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(AccountingTransaction $transaksi)
    {
        $dapurId = $this->getDapurId();
        $this->authorizeTransaction($transaksi, $dapurId);

        $transaksi->load([
            'shortages.laporanKekurangan.templateItem',
            'category',
        ]);

        $periods      = AccountingPeriod::forDapur($dapurId)->where('status', 'open')->orderByDesc('year')->get();
        $categories   = AccountingCategory::forDapur($dapurId)->orderBy('name')->get();
        $cashAccounts = CashAccount::forDapur($dapurId)->orderBy('name')->get();

        return view('akuntan.transaksi.edit', compact('transaksi', 'periods', 'categories', 'cashAccounts'));
    }

    public function update(Request $request, AccountingTransaction $transaksi)
    {
        $dapurId = $this->getDapurId();
        $this->authorizeTransaction($transaksi, $dapurId);

        if (!$transaksi->period->isOpen()) {
            return back()->withErrors(['period_id' => 'Periode sudah ditutup, transaksi tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'period_id'       => 'required|exists:accounting_periods,id',
            'date'            => 'required|date',
            'no_bukti'        => 'nullable|string|max:50',
            'description'     => 'required|string|max:255',
            'category_id'     => 'required|exists:accounting_categories,id',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'debit'           => 'nullable|numeric|min:0',
            'credit'          => 'nullable|numeric|min:0',
        ]);

        $debit  = (float) ($validated['debit']  ?? 0);
        $credit = (float) ($validated['credit'] ?? 0);

        if ($debit > 0 && $credit > 0) {
            throw ValidationException::withMessages(['debit' => 'Debit dan kredit tidak boleh diisi bersamaan.']);
        }
        if ($debit <= 0 && $credit <= 0) {
            throw ValidationException::withMessages(['debit' => 'Salah satu dari debit atau kredit harus lebih besar dari nol.']);
        }

        $period = AccountingPeriod::findOrFail($validated['period_id']);
        $date   = \Carbon\Carbon::parse($validated['date']);
        if ($date->lt($period->start_date) || $date->gt($period->end_date)) {
            throw ValidationException::withMessages([
                'date' => "Tanggal harus berada dalam rentang periode ({$period->start_date->format('d/m/Y')} - {$period->end_date->format('d/m/Y')}).",
            ]);
        }

        $transaksi->update([
            'period_id'       => $period->id,
            'date'            => $validated['date'],
            'month'           => $date->month,
            'no_bukti'        => $validated['no_bukti'],
            'description'     => $validated['description'],
            'category_id'     => $validated['category_id'],
            'cash_account_id' => $validated['cash_account_id'],
            'debit'           => $debit,
            'credit'          => $credit,
        ]);

        return redirect()->route('akuntan.transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(AccountingTransaction $transaksi)
    {
        $dapurId = $this->getDapurId();
        $this->authorizeTransaction($transaksi, $dapurId);

        if (!$transaksi->period->isOpen()) {
            return back()->withErrors(['error' => 'Periode sudah ditutup, transaksi tidak dapat dihapus.']);
        }

        $transaksi->delete();
        return redirect()->route('akuntan.transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function getBalance(Request $request)
    {
        $dapurId = $this->getDapurId();
        $periodId = $request->period_id;
        $cashAccountId = $request->cash_account_id;
        $excludeId = $request->exclude_id;

        if (!$periodId) return response()->json(['current_balance' => 0]);

        $period = AccountingPeriod::forDapur($dapurId)->findOrFail($periodId);
        
        $query = AccountingTransaction::forPeriod($periodId);
        if ($cashAccountId) {
            $query->where('cash_account_id', $cashAccountId);
        }
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');

        $openingBalance = 0;
        if ($cashAccountId) {
            $balance = AccountingBalance::where('period_id', $periodId)
                ->where('cash_account_id', $cashAccountId)
                ->first();
            $openingBalance = $balance ? (float)$balance->opening_balance : 0;
        } else {
            $openingBalance = (float)AccountingBalance::where('period_id', $periodId)->sum('opening_balance');
        }

        $currentBalance = $openingBalance + $totalDebit - $totalCredit;

        $accountName = $cashAccountId ? CashAccount::find($cashAccountId)?->name : 'Semua Akun';

        return response()->json([
            'current_balance' => (float)$currentBalance,
            'cash_account_name' => $accountName,
        ]);
    }

    private function authorizeTransaction(AccountingTransaction $transaksi, int $dapurId): void
    {
        if ($transaksi->period->id_dapur != $dapurId) {
            abort(403);
        }
    }

    public function getPendingShortages(Request $request)
    {
        $dapurId = $this->getDapurId();
        $transaksiId = $request->get('transaksi_id');
        $periodId = $request->get('period_id');
        
        $period = AccountingPeriod::find($periodId);
        $startDate = $period ? $period->start_date : null;
        $endDate = $period ? $period->end_date : null;

        if ($transaksiId) {
            $transaksi = TransaksiDapur::where('id_dapur', $dapurId)
                ->with(['laporanKekuranganStock' => function ($q) {
                    $q->doesntHave('accountingTransactionShortages')->with('templateItem');
                }])
                ->findOrFail($transaksiId);

            $shortages = $transaksi->laporanKekuranganStock->map(function ($item) {
                return [
                    'id_laporan' => $item->id_laporan,
                    'nama_bahan' => $item->templateItem->nama_bahan,
                    'jumlah_kurang' => $item->jumlah_kurang,
                    'satuan' => $item->satuan,
                ];
            });

            return response()->json($shortages);
        }

        $query = TransaksiDapur::where('id_dapur', $dapurId)
            ->whereHas('laporanKekuranganStock', function ($q) {
                $q->doesntHave('accountingTransactionShortages');
            });

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        }

        $transaksiList = $query->orderByDesc('tanggal_transaksi')
            ->get();

        $formattedList = $transaksiList->map(function ($t) {
            return [
                'id_transaksi' => $t->id_transaksi,
                'label' => "ID #{$t->id_transaksi} - " . ($t->keterangan ? str($t->keterangan)->limit(30) : 'Transaksi Dapur') . " (" . $t->tanggal_transaksi->format('d M Y') . ")"
            ];
        });

        return response()->json($formattedList);
    }
}
