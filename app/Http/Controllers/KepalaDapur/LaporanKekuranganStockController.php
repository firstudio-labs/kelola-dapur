<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\LaporanKekuranganStock;
use App\Models\KepalaDapur;
use App\Models\TransaksiDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKekuranganStockController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur.');
        }

        $id_dapur = $kepalaDapur->id_dapur;

        $query = TransaksiDapur::where('id_dapur', $id_dapur)
            ->whereHas('laporanKekuranganStock')
            ->with([
                'laporanKekuranganStock.templateItem',
                'createdBy',
                'detailTransaksiDapur.menuMakanan'
            ]);

        if ($request->filled('status')) {
            $query->whereHas('laporanKekuranganStock', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_paket', 'like', '%' . $search . '%')
                    ->orWhereHas('createdBy', function ($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            if ($sort === 'created_by') {
                $query->join('users', 'transaksi_dapur.created_by', '=', 'users.id_user')
                    ->orderBy('users.nama', 'asc');
            } else {
                $query->orderBy($sort, 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transaksi = $query->paginate(10);

        $stats = [
            'total' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock')
                ->count(),
            'pending' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'pending');
                })->count(),
            'resolved' => TransaksiDapur::where('id_dapur', $id_dapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'resolved');
                })->count(),
            'total_kekurangan_bahan' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($id_dapur) {
                $q->where('id_dapur', $id_dapur);
            })->count()
        ];

        $currentDapur = $kepalaDapur;

        return view('kepaladapur.laporan-kekurangan.index', compact('transaksi', 'stats', 'currentDapur'));
    }

    public function show(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $transaksi->id_dapur !== $kepalaDapur->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $transaksi->load([
            'detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'laporanKekuranganStock.templateItem',
            'createdBy'
        ]);

        $laporan = $transaksi->laporanKekuranganStock;

        return view('kepaladapur.laporan-kekurangan.show', compact('transaksi', 'laporan'));
    }

    public function resolve(Request $request, LaporanKekuranganStock $laporan)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur.');
        }

        if ($laporan->transaksiDapur->id_dapur !== $kepalaDapur->id_dapur) {
            return redirect()->back()->with('error', 'Transaksi ini bukan dari dapur Anda.');
        }

        if ($laporan->isResolved()) {
            return redirect()->back()->with('error', 'Laporan sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);

        if ($laporan->resolve()) {
            if ($request->filled('catatan')) {
                $laporan->update(['keterangan_resolve' => $request->catatan]);
            }

            return redirect()->back()->with('success', 'Laporan kekurangan berhasil diselesaikan.');
        }

        return redirect()->back()->with('error', 'Gagal menyelesaikan laporan kekurangan.');
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

        return view('kepaladapur.laporan-kekurangan.summary', compact('summary', 'dateFrom', 'dateTo'));
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

    public function exportKekuranganPdf(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $transaksi->id_dapur !== $kepalaDapur->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $laporan = $transaksi->laporanKekuranganStock->load('templateItem');

        $pdf = Pdf::loadView('kepaladapur.laporan-kekurangan.export-pdf', compact('transaksi', 'laporan'));
        return $pdf->download('laporan-kekurangan-' . $transaksi->id_transaksi . '.pdf');
    }

    public function exportKekuranganCsv(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur.');
        }

        if ($transaksi->id_dapur !== $kepalaDapur->id_dapur) {
            return redirect()->back()->with('error', 'Transaksi ini bukan dari dapur Anda.');
        }

        $laporan = $transaksi->laporanKekuranganStock->load('templateItem');

        $filename = 'kekurangan-stok-' . $transaksi->id_transaksi . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($laporan) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Nama Bahan',
                'Jumlah Dibutuhkan',
                'Jumlah Tersedia',
                'Jumlah Kurang',
                'Satuan',
                'Status'
            ]);

            foreach ($laporan as $item) {
                fputcsv($file, [
                    $item->templateItem->nama_bahan,
                    $item->jumlah_dibutuhkan,
                    $item->jumlah_tersedia,
                    $item->jumlah_kurang,
                    $item->satuan,
                    $item->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
