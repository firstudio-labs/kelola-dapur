<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingPeriod;
use App\Models\AccountingTransaction;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BukuKasController extends Controller
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

        $selectedPeriodId = $request->get('period_id');
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());

        if ($activePeriod && $activePeriod->year != $selectedYear) {
            $activePeriod = $periods->first();
        }

        $cashAccounts = CashAccount::forDapur($dapurId)->orderBy('name')->get();
        $selectedAccountId = $request->get('cash_account_id');

        $openingBalance = 0;
        $rows = collect();

        if ($activePeriod) {
            $balanceQuery = AccountingBalance::where('period_id', $activePeriod->id);
            if ($selectedAccountId) {
                $balanceQuery->where('cash_account_id', $selectedAccountId);
            }
            
            $openingBalance = (float) $balanceQuery->sum('opening_balance');

            $query = AccountingTransaction::with(['category', 'cashAccount'])
                ->forPeriod($activePeriod->id)
                ->orderedByDate();

            if ($selectedAccountId) {
                $query->where('cash_account_id', $selectedAccountId);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('no_bukti', 'like', "%{$search}%");
                });
            }

            $transactions = $query->get();

            $running = $openingBalance;
            $rows = $transactions->map(function ($t) use (&$running) {
                $running += (float) $t->debit - (float) $t->credit;
                return (object) [
                    'date'        => $t->date,
                    'no_bukti'    => $t->no_bukti,
                    'description' => $t->description,
                    'debit'       => (float) $t->debit,
                    'credit'      => (float) $t->credit,
                    'saldo'       => $running,
                    'category'    => $t->category,
                    'cashAccount' => $t->cashAccount,
                    'id'          => $t->id,
                ];
            });
        }

        return view('akuntan.buku-kas.index', compact(
            'periods', 'activePeriod', 'openingBalance', 'rows', 'years', 'selectedYear', 'cashAccounts', 'selectedAccountId'
        ));
    }

    public function exportPdf(Request $request)
    {
        $dapurId = $this->getDapurId();
        $dapur   = Auth::user()->userRole->dapur;
        $periods = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();

        $selectedPeriodId = $request->get('period_id');
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());

        $selectedAccountId = $request->get('cash_account_id');
        $selectedAccount = $selectedAccountId ? CashAccount::find($selectedAccountId) : null;

        $openingBalance = 0;
        $rows = collect();

        if ($activePeriod) {
            $balanceQuery = AccountingBalance::where('period_id', $activePeriod->id);
            if ($selectedAccountId) {
                $balanceQuery->where('cash_account_id', $selectedAccountId);
            }
            $openingBalance = (float) $balanceQuery->sum('opening_balance');

            $query = AccountingTransaction::with(['category', 'cashAccount'])
                ->forPeriod($activePeriod->id)->orderedByDate();

            if ($selectedAccountId) {
                $query->where('cash_account_id', $selectedAccountId);
            }

            $transactions = $query->get();

            $running = $openingBalance;
            $rows = $transactions->map(function ($t) use (&$running) {
                $running += (float) $t->debit - (float) $t->credit;
                return (object) [
                    'date'        => $t->date,
                    'no_bukti'    => $t->no_bukti,
                    'description' => $t->description,
                    'debit'       => (float) $t->debit,
                    'credit'      => (float) $t->credit,
                    'saldo'       => $running,
                ];
            });
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('akuntan.buku-kas.pdf', compact(
            'activePeriod', 'openingBalance', 'rows', 'dapur', 'selectedAccount'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('buku-kas-' . ($activePeriod->name ?? 'periode') . '.pdf');
    }
}
