<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingPeriod;
use App\Models\AccountingTransaction;
use App\Models\AccountingBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $dapur = $user->userRole->dapur;
        $dapurId = $dapur->id_dapur;

        // All periods for this dapur
        $periods = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->orderByDesc('start_date')->get();

        // Determine active period (selected or default open)
        $selectedPeriodId = $request->get('period_id');
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : $periods->firstWhere('status', 'open') ?? $periods->first();

        $stats = [
            'opening_balance' => 0,
            'total_debit'     => 0,
            'total_credit'    => 0,
            'closing_balance' => 0,
            'total_transaksi' => 0,
        ];

        $cashAccountStats = [];
        if ($activePeriod) {
            $openingBalance = (float) AccountingBalance::where('period_id', $activePeriod->id)->sum('opening_balance');

            $agg = AccountingTransaction::forPeriod($activePeriod->id)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit, COUNT(*) as total_transaksi')
                ->first();

            $stats = [
                'opening_balance' => $openingBalance,
                'total_debit'     => (float) ($agg->total_debit ?? 0),
                'total_credit'    => (float) ($agg->total_credit ?? 0),
                'closing_balance' => $openingBalance + (float) ($agg->total_debit ?? 0) - (float) ($agg->total_credit ?? 0),
                'total_transaksi' => (int) ($agg->total_transaksi ?? 0),
            ];

            // Breakdown by account
            $accounts = \App\Models\CashAccount::forDapur($dapurId)->get();
            foreach ($accounts as $account) {
                $opening = (float) AccountingBalance::where('period_id', $activePeriod->id)
                    ->where('cash_account_id', $account->id)
                    ->value('opening_balance');
                
                $trans = AccountingTransaction::forPeriod($activePeriod->id)
                    ->where('cash_account_id', $account->id)
                    ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                    ->first();
                
                $balance = $opening + ($trans->debit ?? 0) - ($trans->credit ?? 0);
                
                $cashAccountStats[] = [
                    'name'    => $account->name,
                    'type'    => $account->type,
                    'balance' => $balance,
                ];
            }
        }

        return view('akuntan.dashboard', compact('user', 'dapur', 'periods', 'activePeriod', 'stats', 'cashAccountStats'));
    }
}
