<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingPeriod;
use App\Models\AccountingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BukuPembantuController extends Controller
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

        $selectedGroup = $request->get('group');
        $groups = [
            'dana_bahan_baku' => 'Dana Bahan Baku',
            'dana_operasional' => 'Dana Operasional',
            'dana_insentif_fasilitas' => 'Dana Insentif Fasilitas',
            'pungutan_ppn' => 'Pungutan/Setoran PPN',
            'pungutan_pph21' => 'Pungutan/Setoran PPh 21',
            'pungutan_pph22' => 'Pungutan/Setoran PPh 22',
            'pungutan_pph23' => 'Pungutan/Setoran PPh 23',
            'pungutan_pph4' => 'Pungutan/Setoran PPh pasal 4 ayat (2)',
            'biaya_bahan_baku' => 'Biaya Bahan Baku',
            'biaya_operasional' => 'Biaya Operasional',
            'biaya_insentif_fasilitas' => 'Biaya Insentif Fasilitas',
        ];

        $openingBalance = 0;
        $rows = collect();

        if ($activePeriod) {
            $balance = AccountingBalance::where('period_id', $activePeriod->id)->first();
            $openingBalance = $balance ? (float) $balance->opening_balance : 0;

            $query = AccountingTransaction::with(['category', 'cashAccount'])
                ->forPeriod($activePeriod->id)
                ->orderedByDate();

            if ($selectedGroup) {
                $query->whereHas('category', fn($q) => $q->where('group', $selectedGroup));
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
                ];
            });
        }

        return view('akuntan.buku-pembantu.index', compact(
            'periods', 'activePeriod', 'groups', 'selectedGroup', 'openingBalance', 'rows', 'years', 'selectedYear'
        ));
    }
}
