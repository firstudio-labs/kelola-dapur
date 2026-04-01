<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingPeriod;
use App\Models\AccountingSetting;
use App\Models\AccountingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokumenController extends Controller
{
    private function getDapurId(): int
    {
        return Auth::user()->userRole->id_dapur;
    }

    public function index()
    {
        $dapurId = $this->getDapurId();
        $periods = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();
        $setting = AccountingSetting::where('id_dapur', $dapurId)->first();

        $dokumenTypes = [
            'sptj'       => 'Surat Pernyataan Tanggung Jawab',
            'bapsd'      => 'Berita Acara Pengalihan Sisa Dana',
            'nominatif'  => 'Daftar Nominatif',
        ];

        return view('akuntan.dokumen.index', compact('periods', 'setting', 'dokumenTypes'));
    }

    public function preview(Request $request, string $type)
    {
        $dapurId = $this->getDapurId();
        $dapur   = Auth::user()->userRole->dapur;
        $setting = AccountingSetting::where('id_dapur', $dapurId)->first();

        $selectedPeriodId = $request->get('period_id');
        $periods = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());

        $balance = $activePeriod ? AccountingBalance::where('period_id', $activePeriod->id)->first() : null;
        $openingBalance = $balance ? (float) $balance->opening_balance : 0;

        $transactions = $activePeriod
            ? AccountingTransaction::with(['category', 'cashAccount'])
                ->forPeriod($activePeriod->id)->orderedByDate()->get()
            : collect();

        $views = [
            'sptj'      => 'akuntan.dokumen.sptj',
            'bapsd'     => 'akuntan.dokumen.bapsd',
            'nominatif' => 'akuntan.dokumen.nominatif',
        ];

        $view = $views[$type] ?? null;
        if (!$view) {
            abort(404);
        }

        // Preview renders the raw document HTML in browser
        return view($view, compact('setting', 'activePeriod', 'transactions', 'openingBalance', 'dapur'));
    }

    public function exportPdf(Request $request, string $type)
    {
        $dapurId = $this->getDapurId();
        $dapur   = Auth::user()->userRole->dapur;
        $setting = AccountingSetting::where('id_dapur', $dapurId)->first();

        $selectedPeriodId = $request->get('period_id');
        $periods = AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();
        $activePeriod = $selectedPeriodId
            ? $periods->firstWhere('id', $selectedPeriodId)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());

        $balance = $activePeriod ? AccountingBalance::where('period_id', $activePeriod->id)->first() : null;
        $openingBalance = $balance ? (float) $balance->opening_balance : 0;

        $transactions = $activePeriod
            ? AccountingTransaction::with(['category', 'cashAccount'])
                ->forPeriod($activePeriod->id)->orderedByDate()->get()
            : collect();

        $views = [
            'sptj'      => 'akuntan.dokumen.sptj',
            'bapsd'     => 'akuntan.dokumen.bapsd',
            'nominatif' => 'akuntan.dokumen.nominatif',
        ];

        $view = $views[$type] ?? null;
        if (!$view) abort(404);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, compact(
            'setting', 'activePeriod', 'transactions', 'openingBalance', 'dapur'
        ))->setPaper('a4');

        $labels = [
            'sptj'      => 'SPTJ',
            'bapsd'     => 'BAPSD',
            'nominatif' => 'Nominatif',
        ];

        return $pdf->download($labels[$type] . '-' . ($activePeriod->name ?? 'periode') . '.pdf');
    }
}
