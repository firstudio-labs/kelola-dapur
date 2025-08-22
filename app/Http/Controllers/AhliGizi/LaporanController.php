<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\LaporanKekuranganStock;
use App\Models\TransaksiDapur;
use App\Models\AhliGizi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
            $q->where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user);
        })
            ->with([
                'transaksiDapur.detailTransaksiDapur.menuMakanan',
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
            'total' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
                $q->where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user);
            })->count(),
            'pending' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
                $q->where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user);
            })->where('status', 'pending')->count(),
            'resolved' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
                $q->where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user);
            })->where('status', 'resolved')->count()
        ];

        return view('ahli-gizi.laporan.index', compact('reports', 'stats', 'ahliGizi'));
    }

    public function show(LaporanKekuranganStock $laporan)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (
            !$ahliGizi ||
            $laporan->transaksiDapur->id_dapur !== $ahliGizi->id_dapur ||
            $laporan->transaksiDapur->created_by !== $user->id_user
        ) {
            abort(403, 'Unauthorized');
        }

        $laporan->load([
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'transaksiDapur.createdBy',
            'transaksiDapur.dapur',
            'templateItem'
        ]);

        $currentStock = $laporan->templateItem->getStockByDapur($ahliGizi->id_dapur);

        return view('ahli-gizi.laporan.show', compact('laporan', 'currentStock', 'ahliGizi'));
    }

    public function transaksiWithShortage(Request $request)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $query = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->whereHas('laporanKekuranganStock')
            ->with([
                'laporanKekuranganStock.templateItem',
                'detailTransaksiDapur.menuMakanan',
                'approvalTransaksi'
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

        if ($request->filled('shortage_status')) {
            $query->whereHas('laporanKekuranganStock', function ($q) use ($request) {
                $q->where('status', $request->shortage_status);
            });
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'total_with_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereHas('laporanKekuranganStock')
                ->count(),
            'pending_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'pending');
                })
                ->count(),
            'resolved_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'resolved');
                })
                ->count(),
        ];

        return view('ahli-gizi.laporan.transaksi-with-shortage', compact('transaksi', 'stats', 'ahliGizi'));
    }

    public function dashboardSummary()
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $thisMonth = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        $summary = [
            'total_transaksi' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->count(),

            'transaksi_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->whereHas('laporanKekuranganStock')
                ->count(),

            'transaksi_approved' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->count(),

            'transaksi_rejected' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'rejected')
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->count(),

            'draft_transaksi' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'draft')
                ->count(),

            'laporan_pending' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
                $q->where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user);
            })->where('status', 'pending')->count(),

            'bahan_sering_kurang' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
                $q->where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user);
            })
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->with('templateItem')
                ->get()
                ->groupBy('id_template_item')
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'nama_bahan' => $first->templateItem->nama_bahan,
                        'jumlah_kejadian' => $group->count(),
                        'total_kekurangan' => $group->sum('jumlah_kurang'),
                        'satuan' => $first->satuan,
                        'status_terakhir' => $group->sortByDesc('created_at')->first()->status
                    ];
                })
                ->sortByDesc('jumlah_kejadian')
                ->take(5),

            'approval_rate' => $this->calculateApprovalRate($ahliGizi, $user),

            'pending_approval' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'pending_approval')
                ->count(),
        ];

        return $summary;
    }

    public function getSummaryJson()
    {
        $summary = $this->dashboardSummary();
        return response()->json($summary);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($user, $ahliGizi) {
            $q->where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user);
        })->with(['transaksiDapur', 'templateItem']);

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

        $filename = 'laporan-kekurangan-ahli-gizi-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Tanggal Laporan',
                'ID Transaksi',
                'Nama Paket',
                'Nama Bahan',
                'Jumlah Dibutuhkan',
                'Jumlah Tersedia',
                'Jumlah Kurang',
                'Satuan',
                'Status Laporan',
                'Tanggal Diselesaikan',
                'Keterangan'
            ]);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->created_at->format('Y-m-d H:i:s'),
                    $report->id_transaksi,
                    $report->transaksiDapur->nama_paket ?? '-',
                    $report->templateItem->nama_bahan,
                    $report->jumlah_dibutuhkan,
                    $report->jumlah_tersedia,
                    $report->jumlah_kurang,
                    $report->satuan,
                    $report->status,
                    $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i:s') : '-',
                    $report->keterangan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateApprovalRate(AhliGizi $ahliGizi, $user)
    {
        $thisMonth = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        $totalTransaksi = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->whereIn('status', ['completed', 'rejected'])
            ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
            ->count();

        if ($totalTransaksi == 0) {
            return 0;
        }

        $approvedTransaksi = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
            ->count();

        return round(($approvedTransaksi / $totalTransaksi) * 100, 2);
    }

    public function getMonthlyTrend()
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $last6Months = collect(range(0, 5))->map(function ($i) use ($ahliGizi, $user) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            return [
                'month' => $month->format('M Y'),
                'total_transaksi' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count(),
                'total_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->whereHas('laporanKekuranganStock')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count(),
                'total_approved' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count(),
            ];
        })->reverse();

        return response()->json($last6Months);
    }
}
