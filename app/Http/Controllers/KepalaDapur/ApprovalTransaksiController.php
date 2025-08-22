<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\ApprovalTransaksi;
use App\Models\TransaksiDapur;
use App\Models\KepalaDapur;
use App\Models\LaporanKekuranganStock;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalTransaksiController extends Controller
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

        $query = ApprovalTransaksi::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->with([
                'transaksiDapur.detailTransaksiDapur.menuMakanan',
                'transaksiDapur.createdBy',
                'ahliGizi.user'
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

        if ($request->filled('ahli_gizi')) {
            $query->where('id_ahli_gizi', $request->ahli_gizi);
        }

        $approvals = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'pending' => ApprovalTransaksi::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
                ->where('status', 'pending')->count(),
            'approved' => ApprovalTransaksi::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
                ->where('status', 'approved')->count(),
            'rejected' => ApprovalTransaksi::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
                ->where('status', 'rejected')->count(),
        ];

        return view('kepala-dapur.approval-transaksi.index', compact('approvals', 'stats', 'kepalaDapur'));
    }

    public function show(ApprovalTransaksi $approval)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Unauthorized');
        }

        $approval->load([
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'transaksiDapur.laporanKekuranganStock.templateItem',
            'transaksiDapur.createdBy',
            'transaksiDapur.dapur',
            'ahliGizi.user',
            'kepalaDapur.user'
        ]);

        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

        $bahanKebutuhan = $this->calculateIngredientNeeds($approval->transaksiDapur);

        return view('kepala-dapur.approval-transaksi.show', compact('approval', 'stockCheck', 'bahanKebutuhan', 'kepalaDapur'));
    }

    public function approve(Request $request, ApprovalTransaksi $approval)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Unauthorized');
        }

        if (!$approval->isPending()) {
            return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                ->with('error', 'Input Paket Menu sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
            'konfirmasi_stock' => 'required|accepted'
        ], [
            'konfirmasi_stock.accepted' => 'Anda harus mengkonfirmasi bahwa stock mencukupi untuk menyetujui transaksi ini.'
        ]);

        DB::beginTransaction();
        try {
            $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

            if (!$stockCheck['can_produce']) {
                DB::rollback();
                return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                    ->with('error', 'Stock tidak mencukupi untuk menyetujui Input Paket Menu ini. Silakan cek kembali ketersediaan stock.')
                    ->with('shortages', $stockCheck['shortages']);
            }

            $success = $approval->approve($request->catatan_approval);

            if ($success) {
                $this->reduceStockItems($approval->transaksiDapur);
                $approval->transaksiDapur->update([
                    'status' => 'completed',
                    'tanggal_diproses' => now()
                ]);

                DB::commit();
                return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                    ->with('success', 'Input Paket Menu berhasil disetujui dan diproses. Stock telah dikurangi sesuai kebutuhan.');
            } else {
                DB::rollback();
                return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                    ->with('error', 'Gagal memproses persetujuan Input Paket Menu.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving transaksi: ' . $e->getMessage());
            return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                ->with('error', 'Terjadi error saat memproses persetujuan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ApprovalTransaksi $approval)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Unauthorized');
        }

        if (!$approval->isPending()) {
            return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                ->with('error', 'Input Paket Menu sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_approval' => 'required|string|max:500'
        ], [
            'catatan_approval.required' => 'Alasan penolakan harus diisi.'
        ]);

        DB::beginTransaction();
        try {
            $success = $approval->reject($request->catatan_approval);

            if ($success) {
                $approval->transaksiDapur->update([
                    'status' => 'rejected',
                    'tanggal_diproses' => now()
                ]);

                DB::commit();
                return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                    ->with('success', 'Input Paket Menu berhasil ditolak.');
            }

            DB::rollback();
            return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                ->with('error', 'Gagal menolak Input Paket Menu.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error rejecting transaksi: ' . $e->getMessage());
            return redirect()->route('kepala-dapur.approval-transaksi.show', $approval)
                ->with('error', 'Terjadi error saat menolak: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_ids' => 'required|array|min:1',
            'approval_ids.*' => 'exists:approval_transaksi,id_approval_transaksi',
            'catatan_approval' => 'nullable|string|max:500'
        ]);

        $approvals = ApprovalTransaksi::whereIn('id_approval_transaksi', $request->approval_ids)
            ->where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->where('status', 'pending')
            ->get();

        if ($approvals->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada Input Paket Menu yang valid untuk diproses.');
        }

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($approvals as $approval) {
                if ($request->action === 'approve') {
                    $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

                    if (!$stockCheck['can_produce']) {
                        $errors[] = "Input Paket #{$approval->transaksiDapur->id_transaksi}: Stock tidak mencukupi";
                        continue;
                    }

                    if ($approval->approve($request->catatan_approval)) {
                        $this->reduceStockItems($approval->transaksiDapur);

                        $approval->transaksiDapur->update([
                            'status' => 'completed',
                            'tanggal_diproses' => now()
                        ]);

                        $successCount++;
                    } else {
                        $errors[] = "Input Paket #{$approval->transaksiDapur->id_transaksi}: Gagal disetujui";
                    }
                } else {
                    if (!$request->filled('catatan_approval')) {
                        $errors[] = "Alasan penolakan harus diisi untuk aksi bulk reject";
                        continue;
                    }

                    if ($approval->reject($request->catatan_approval)) {
                        $approval->transaksiDapur->update([
                            'status' => 'rejected',
                            'tanggal_diproses' => now()
                        ]);

                        $successCount++;
                    } else {
                        $errors[] = "Input Paket #{$approval->transaksiDapur->id_transaksi}: Gagal ditolak";
                    }
                }
            }

            DB::commit();

            $actionText = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "{$successCount} Input Paket Menu berhasil {$actionText}";

            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return redirect()->back()
                ->with($successCount > 0 ? 'success' : 'error', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error bulk action approval: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }

    private function reduceStockItems(TransaksiDapur $transaksi)
    {
        $kebutuhan = $this->calculateIngredientNeeds($transaksi);

        foreach ($kebutuhan as $idTemplate => $data) {
            $stockItem = StockItem::where('id_template_item', $idTemplate)
                ->where('id_dapur', $transaksi->id_dapur)
                ->first();

            if ($stockItem) {
                $stockItem->decrement('jumlah_stock', $data['total_kebutuhan']);

                Log::info("Stock reduced for template {$idTemplate}: {$data['total_kebutuhan']} {$data['satuan']}");
            }
        }
    }

    private function calculateIngredientNeeds(TransaksiDapur $transaksi)
    {
        $kebutuhan = [];

        foreach ($transaksi->detailTransaksiDapur as $detail) {
            foreach ($detail->menuMakanan->bahanMenu as $bahanMenu) {
                $idTemplate = $bahanMenu->id_template_item;
                $totalKebutuhan = $bahanMenu->jumlah_per_porsi * $detail->jumlah_porsi;

                if (!isset($kebutuhan[$idTemplate])) {
                    $kebutuhan[$idTemplate] = [
                        'nama_bahan' => $bahanMenu->templateItem->nama_bahan,
                        'satuan' => $bahanMenu->satuan,
                        'total_kebutuhan' => 0,
                        'detail_penggunaan' => []
                    ];
                }

                $kebutuhan[$idTemplate]['total_kebutuhan'] += $totalKebutuhan;
                $kebutuhan[$idTemplate]['detail_penggunaan'][] = [
                    'menu' => $detail->menuMakanan->nama_menu,
                    'tipe_porsi' => $detail->tipe_porsi,
                    'jumlah_porsi' => $detail->jumlah_porsi,
                    'kebutuhan_per_porsi' => $bahanMenu->jumlah_per_porsi,
                    'total_kebutuhan' => $totalKebutuhan
                ];
            }
        }

        return $kebutuhan;
    }

    public function shortageReports(Request $request)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur) {
            abort(403, 'Unauthorized');
        }

        $query = LaporanKekuranganStock::whereHas('transaksiDapur', function ($query) use ($kepalaDapur) {
            $query->where('id_dapur', $kepalaDapur->id_dapur);
        })->with([
            'transaksiDapur.createdBy',
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

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'pending' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })->where('status', 'pending')->count(),
            'resolved' => LaporanKekuranganStock::whereHas('transaksiDapur', function ($q) use ($kepalaDapur) {
                $q->where('id_dapur', $kepalaDapur->id_dapur);
            })->where('status', 'resolved')->count(),
        ];

        return view('kepala-dapur.shortage-reports.index', compact('reports', 'stats', 'kepalaDapur'));
    }

    public function resolveShortage(LaporanKekuranganStock $report)
    {
        $user = Auth::user();
        $kepalaDapur = KepalaDapur::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$kepalaDapur || $report->transaksiDapur->id_dapur !== $kepalaDapur->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($report->isResolved()) {
            return redirect()->back()
                ->with('error', 'Laporan kekurangan sudah diselesaikan sebelumnya.');
        }

        if ($report->resolve()) {
            return redirect()->back()
                ->with('success', 'Laporan kekurangan berhasil diselesaikan.');
        }

        return redirect()->back()
            ->with('error', 'Gagal menyelesaikan laporan kekurangan.');
    }
}
