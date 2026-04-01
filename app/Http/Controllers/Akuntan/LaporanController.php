<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AccountingBalance;
use App\Models\AccountingPeriod;
use App\Models\AccountingTransaction;
use App\Models\AccountingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    private function getDapurId(): int
    {
        return Auth::user()->userRole->id_dapur;
    }

    private function getPeriodList(int $dapurId)
    {
        return AccountingPeriod::forDapur($dapurId)->orderByDesc('year')->get();
    }

    private function resolveActivePeriod($periods, $request)
    {
        $id = $request->get('period_id');
        return $id
            ? $periods->firstWhere('id', $id)
            : ($periods->firstWhere('status', 'open') ?? $periods->first());
    }

    // 5.1 Resume Penerimaan & Pengeluaran
    public function resume(Request $request)
    {
        $dapurId = $this->getDapurId();
        $periods = $this->getPeriodList($dapurId);
        $activePeriod = $this->resolveActivePeriod($periods, $request);

        $settings = \App\Models\AccountingSetting::firstOrCreate(
            ['id_dapur' => $dapurId],
            [
                'institution_name' => 'Nama Lembaga',
                'address' => 'Alamat Lembaga',
                'head_name' => 'Nama Pimpinan',
                'treasurer_name' => 'Nama Akuntan',
                'report_location' => 'Lokasi',
                'report_date' => now()
            ]
        );

        $incomes = collect();
        $expenses = collect();
        $totalPenerimaan = 0;
        $totalPengeluaran = 0;
        $cash = 0;
        $bank = 0;
        $openingBalance = 0; // TBD if we bring back Balance

        if ($activePeriod) {
            $periodId = $activePeriod->id;

            // Get balance if required in the future (Left as 0 for now as per current specs where opening isn't explicitly requested to be added)
            // $balance = AccountingBalance::where('period_id', $periodId)->first();
            // $openingBalance = $balance ? (float) $balance->opening_balance : 0;

            // Dynamic Incomes (including taxes)
            $incomes = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', function($q) {
                    $q->where('type', 'income')->orWhere('is_tax', true);
                })
                ->join('accounting_categories', 'accounting_transactions.category_id', '=', 'accounting_categories.id')
                ->selectRaw('accounting_categories.name, SUM(debit) as total')
                ->groupBy('accounting_categories.name')
                ->orderBy('accounting_categories.name')
                ->get();
            
            $totalPenerimaan = $incomes->sum('total');

            // Dynamic Expenses (credit)
            $expenses = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', function($q) {
                    $q->where('type', 'expense')->where('is_tax', false);
                })
                ->join('accounting_categories', 'accounting_transactions.category_id', '=', 'accounting_categories.id')
                ->selectRaw('accounting_categories.name, SUM(credit) as total')
                ->groupBy('accounting_categories.name')
                ->orderBy('accounting_categories.name')
                ->get();

            $totalPengeluaran = $expenses->sum('total');

            $cash = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('cashAccount', fn($q) => $q->where('type', 'cash'))
                ->sum(\Illuminate\Support\Facades\DB::raw('debit - credit'));

            $bank = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('cashAccount', fn($q) => $q->where('type', 'bank'))
                ->sum(\Illuminate\Support\Facades\DB::raw('debit - credit'));
        }

        $saldo = $totalPenerimaan - $totalPengeluaran;

        return view('akuntan.laporan.resume', compact(
            'periods', 'activePeriod', 'settings',
            'incomes', 'expenses',
            'totalPenerimaan', 'totalPengeluaran', 'saldo',
            'cash', 'bank'
        ));
    }

    // 5.2 Laporan Penggunaan Anggaran
    public function anggaran(Request $request)
    {
        $dapurId = $this->getDapurId();
        $periods = $this->getPeriodList($dapurId);
        $activePeriod = $this->resolveActivePeriod($periods, $request);

        $settings = \App\Models\AccountingSetting::firstOrCreate(
            ['id_dapur' => $dapurId],
            [
                'institution_name' => 'Nama Lembaga',
                'address' => 'Alamat Lembaga',
                'foundation_head' => 'Ketua Yayasan',
                'head_name' => 'Nama Pimpinan',
                'treasurer_name' => 'Nama Akuntan',
                'report_location' => 'Lokasi',
                'report_date' => now()
            ]
        );

        $nomor = '...'; // Bisa di-generate atau di-set statis sementara

        $bahanBakuMasuk = 0;
        $operasionalMasuk = 0;
        $fasilitasMasuk = 0;
        
        $bahanBakuKeluar = 0;
        $operasionalKeluar = 0;
        $fasilitasKeluar = 0;

        if ($activePeriod) {
            $periodId = $activePeriod->id;

            // MASUK (Dana Diajukan: Total Debit per kategori)
            $bahanBakuMasuk = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'dana_bahan_baku'))
                ->sum('debit');

            $operasionalMasuk = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'dana_operasional'))
                ->sum('debit');

            $fasilitasMasuk = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'dana_insentif_fasilitas'))
                ->sum('debit');

            // KELUAR (Dana Terealisasi: Total Credit per kategori)
            $bahanBakuKeluar = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'biaya_bahan_baku'))
                ->sum('credit');

            $operasionalKeluar = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'biaya_operasional'))
                ->sum('credit');

            $fasilitasKeluar = AccountingTransaction::where('period_id', $periodId)
                ->whereHas('category', fn($q) => $q->where('group', 'biaya_insentif_fasilitas'))
                ->sum('credit');
        }

        // SISA PER KATEGORI
        $sisaBahanBaku = $bahanBakuMasuk - $bahanBakuKeluar;
        $sisaOperasional = $operasionalMasuk - $operasionalKeluar;
        $sisaFasilitas = $fasilitasMasuk - $fasilitasKeluar;

        // TOTAL
        $totalMasuk = $bahanBakuMasuk + $operasionalMasuk + $fasilitasMasuk;
        $totalKeluar = $bahanBakuKeluar + $operasionalKeluar + $fasilitasKeluar;
        $totalSisa = $totalMasuk - $totalKeluar;

        return view('akuntan.laporan.anggaran', compact(
            'periods', 'activePeriod', 'settings', 'nomor',
            'bahanBakuMasuk', 'operasionalMasuk', 'fasilitasMasuk',
            'bahanBakuKeluar', 'operasionalKeluar', 'fasilitasKeluar',
            'sisaBahanBaku', 'sisaOperasional', 'sisaFasilitas',
            'totalMasuk', 'totalKeluar', 'totalSisa'
        ));
    }

    // 5.3 Catatan Pengeluaran Bulanan
    public function bulanan(Request $request)
    {
        $dapurId = $this->getDapurId();
        $periods = $this->getPeriodList($dapurId);
        $activePeriod = $this->resolveActivePeriod($periods, $request);

        $rows = collect();
        if ($activePeriod) {
            $rows = AccountingTransaction::forPeriod($activePeriod->id)
                ->join('accounting_categories', 'accounting_transactions.category_id', '=', 'accounting_categories.id')
                ->where('accounting_categories.type', 'expense')
                ->selectRaw('accounting_transactions.month, SUM(credit) as total_pengeluaran, COUNT(*) as jumlah_transaksi')
                ->groupBy('accounting_transactions.month')
                ->orderBy('accounting_transactions.month')
                ->get()
                ->map(function ($r) {
                    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $r->month_name = $bulan[$r->month] ?? '-';
                    return $r;
                });
        }

        return view('akuntan.laporan.bulanan', compact('periods', 'activePeriod', 'rows'));
    }
}
