<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\LaporanKekuranganStock;
use App\Models\KepalaDapur;
use App\Models\TransaksiDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanKekuranganStockController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
            $q->where('id_dapur', $kepalaDapur->id_dapur);
        })
            ->with([
                'transaksiDapur.createdBy',
                'transaksiDapur.detailTransaksiDapur',
                'templateItem'
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereHas('templateItem', function ($q) use ($request) {
                $q->where('nama_bahan', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'total' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })->count(),
            'pending' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })->where('status', 'pending')->count(),
            'resolved' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })->where('status', 'resolved')->count()
        ];

        return view('kepala-dapur.laporan-kekurangan.index', compact('reports', 'stats'));
    }

    public function show(LaporanKekuranganStock $laporan)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $laporan->transaksiDapur->id_dapur !== $kepalaDapur->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $laporan->load([
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'transaksiDapur.createdBy',
            'templateItem'
        ]);

        $currentStock = $laporan->templateItem->getStockByDapur($kepalaDapur->id_dapur);

        return view('kepala-dapur.laporan-kekurangan.show', compact('laporan', 'currentStock'));
    }

    public function resolve(Request $request, LaporanKekuranganStock $laporan)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $laporan->transaksiDapur->id_dapur !== $kepalaDapur->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($laporan->isResolved()) {
            return redirect()->back()
                ->with('error', 'Laporan sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        if ($laporan->resolve()) {
            if ($request->filled('catatan')) {
                $laporan->update(['keterangan_resolve' => $request->catatan]);
            }

            return redirect()->back()
                ->with('success', 'Laporan kekurangan berhasil diselesaikan.');
        }

        return redirect()->back()
            ->with('error', 'Gagal menyelesaikan laporan kekurangan.');
    }

    public function bulkResolve(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'laporan_ids' => 'required|array|min:1',
            'laporan_ids.*' => 'exists:laporan_kekurangan_stock,id_laporan',
            'catatan' => 'nullable|string|max:500'
        ]);

        $laporans = LaporanKekuranganStock::whereIn('id_laporan', $request->laporan_ids)
            ->whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })
            ->where('status', 'pending')
            ->get();

        if ($laporans->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada laporan yang valid untuk diselesaikan.');
        }

        $successCount = 0;
        foreach ($laporans as $laporan) {
            if ($laporan->resolve()) {
                if ($request->filled('catatan')) {
                    $laporan->update(['keterangan_resolve' => $request->catatan]);
                }
                $successCount++;
            }
        }

        return redirect()->back()
            ->with('success', "{$successCount} laporan berhasil diselesaikan.");
    }

    public function summary(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));

        $reports = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
            $q->where('id_dapur', $kepalaDapur->id_dapur);
        })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['templateItem', 'transaksiDapur'])
            ->get();

        $summary = $reports->groupBy('id_template_item')->map(function ($group) {
            $first = $group->first();
            return [
                'nama_bahan' => $first->templateItem->nama_bahan,
                'satuan' => $first->satuan,
                'total_kekurangan' => $group->sum('jumlah_kurang'),
                'total_dibutuhkan' => $group->sum('jumlah_dibutuhkan'),
                'jumlah_kejadian' => $group->count(),
                'status_terakhir' => $group->sortByDesc('created_at')->first()->status
            ];
        });

        return view('kepala-dapur.laporan-kekurangan.summary', compact('summary', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
            $q->where('id_dapur', $kepalaDapur->id_dapur);
        })
            ->with(['transaksiDapur.createdBy', 'templateItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        $filename = 'laporan-kekurangan-stock-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Tanggal',
                'ID Transaksi',
                'Nama Bahan',
                'Jumlah Dibutuhkan',
                'Jumlah Tersedia',
                'Jumlah Kurang',
                'Satuan',
                'Status',
                'Dibuat Oleh'
            ]);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->created_at->format('Y-m-d H:i:s'),
                    $report->id_transaksi,
                    $report->templateItem->nama_bahan,
                    $report->jumlah_dibutuhkan,
                    $report->jumlah_tersedia,
                    $report->jumlah_kurang,
                    $report->satuan,
                    $report->status,
                    $report->transaksiDapur->createdBy->nama
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
