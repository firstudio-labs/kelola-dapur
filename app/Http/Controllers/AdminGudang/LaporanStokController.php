<?php

namespace App\Http\Controllers\AdminGudang;

use App\Http\Controllers\Controller;
use App\Models\ProduksiHandlerBahan;
use App\Models\AdminGudang;
use App\Models\Dapur;
use App\Models\StockItem;
use App\Models\ApprovalStockItem;
use App\Models\LaporanKekuranganStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanStokController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang || $dapur->id_dapur !== $adminGudang->id_dapur) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang di dapur ini.');
        }

        $id_dapur = $adminGudang->id_dapur;

        $query = ProduksiHandlerBahan::whereHas('orderProduksi', function ($q) use ($id_dapur) {
            $q->where('id_dapur', $id_dapur);
        })->with([
            'orderProduksi.transaksiDapur.createdBy',
            'orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'templateItem'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');

        $laporan = $query->paginate(15);

        $stats = [
            'total' => ProduksiHandlerBahan::whereHas('orderProduksi', function ($q) use ($id_dapur) {
                $q->where('id_dapur', $id_dapur);
            })->count(),
            'pending' => ProduksiHandlerBahan::whereHas('orderProduksi', function ($q) use ($id_dapur) {
                $q->where('id_dapur', $id_dapur);
            })->where('status', 'pending')->count(),
            'resolved' => ProduksiHandlerBahan::whereHas('orderProduksi', function ($q) use ($id_dapur) {
                $q->where('id_dapur', $id_dapur);
            })->where('status', 'resolved')->count(),
        ];

        $orderIds = $laporan->pluck('id_order')->unique();
        $itemIds = $laporan->pluck('id_template_item')->unique();
        
        $histories = ProduksiHandlerBahan::whereIn('id_order', $orderIds)
            ->whereIn('id_template_item', $itemIds)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->id_order . '_' . $item->id_template_item;
            });

        $stockItems = StockItem::where('id_dapur', $id_dapur)
            ->whereIn('id_template_item', $itemIds)
            ->get()
            ->keyBy('id_template_item');

        $currentDapur = $adminGudang;

        return view('admingudang.laporan-stok.index', compact('laporan', 'stats', 'currentDapur', 'dapur', 'histories', 'stockItems'));
    }

    public function resolve(Request $request, Dapur $dapur, ProduksiHandlerBahan $laporan)
    {
        $user = Auth::user();
        $adminGudang = AdminGudang::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$adminGudang || $dapur->id_dapur !== $adminGudang->id_dapur) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Admin Gudang di dapur ini.');
        }

        if ($laporan->orderProduksi->id_dapur !== $adminGudang->id_dapur) {
            return redirect()->back()->with('error', 'Laporan ini bukan dari dapur Anda.');
        }

        if ($laporan->status !== 'pending') {
            return redirect()->back()->with('error', 'Laporan sudah diproses sebelumnya.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_admin' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                $stockItem = StockItem::where('id_dapur', $dapur->id_dapur)
                    ->where('id_template_item', $laporan->id_template_item)
                    ->first();

                if (!$stockItem) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Item stok tidak ditemukan di dapur ini.');
                }

                $orderStatus = $laporan->orderProduksi->status ?? 'belum_dibuat';
                $isPreProduksi = in_array($orderStatus, ['belum_dibuat', 'stok_kurang']);

                if ($laporan->jenis === 'kelebihan') {
                    if (!$isPreProduksi) {
                        $stockItem->addStock($laporan->jumlah);
                    }
                } else if ($laporan->jenis === 'kekurangan') {
                    $available = (float) $stockItem->jumlah;
                    $requested = (float) $laporan->jumlah;
                    $idTransaksi = $laporan->orderProduksi->id_transaksi ?? null;

                    if ($isPreProduksi) {
                        $orderProduksi = $laporan->orderProduksi;
                        $transaksi = $orderProduksi->transaksiDapur;

                        $baseNeeded = 0;
                        if ($transaksi) {
                            $ingredients = $transaksi->getRequiredIngredients();
                            foreach ($ingredients as $ing) {
                                if ($ing['id_template_item'] == $laporan->id_template_item) {
                                    $baseNeeded = (float) $ing['needed'];
                                    break;
                                }
                            }
                        }

                        $approvedKekurangan = ProduksiHandlerBahan::where('id_order', $laporan->id_order)
                            ->where('id_template_item', $laporan->id_template_item)
                            ->where('jenis', 'kekurangan')
                            ->where('status', 'resolved')
                            ->sum('jumlah');

                        $approvedKelebihan = ProduksiHandlerBahan::where('id_order', $laporan->id_order)
                            ->where('id_template_item', $laporan->id_template_item)
                            ->where('jenis', 'kelebihan')
                            ->where('status', 'resolved')
                            ->sum('jumlah');

                        $totalNeeded = $baseNeeded + $approvedKekurangan + $requested - $approvedKelebihan;
                        $estimasiSisa = $available - $totalNeeded;

                        if ($estimasiSisa < 0 && $idTransaksi) {
                            $sisaKekurangan = abs($estimasiSisa);
                            LaporanKekuranganStock::create([
                                'id_transaksi' => $idTransaksi,
                                'id_template_item' => $laporan->id_template_item,
                                'jumlah_dibutuhkan' => $totalNeeded,
                                'jumlah_tersedia' => $available,
                                'jumlah_kurang' => $sisaKekurangan,
                                'satuan' => $stockItem->templateItem->satuan ?? '',
                                'status' => 'pending',
                                'id_handler' => $laporan->id_handler,
                            ]);
                        }
                    } else {
                        if ($requested > $available) {
                            $sisaKekurangan = $requested - $available;
                            if ($available > 0) {
                                $stockItem->reduceStock($available);
                            }

                            if ($idTransaksi) {
                                LaporanKekuranganStock::create([
                                    'id_transaksi' => $idTransaksi,
                                    'id_template_item' => $laporan->id_template_item,
                                    'jumlah_dibutuhkan' => $requested,
                                    'jumlah_tersedia' => $available,
                                    'jumlah_kurang' => $sisaKekurangan,
                                    'satuan' => $stockItem->templateItem->satuan ?? '',
                                    'status' => 'pending',
                                    'id_handler' => $laporan->id_handler,
                                ]);
                            }
                        } else {
                            $stockItem->reduceStock($requested);
                        }
                    }
                }

                $laporan->status = 'resolved';
            } else {
                $laporan->status = 'rejected';
            }
            if ($request->filled('catatan_admin')) {
                $namaAdmin = $adminGudang->nama_lengkap ?? $user->nama;
                $laporan->catatan = $laporan->catatan . "\n[" . $namaAdmin . "]: " . $request->catatan_admin;
            }

            $laporan->save();
            DB::commit();

            $statusMsg = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            return redirect()->back()->with('success', "Laporan handler bahan berhasil $statusMsg.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to resolve handler bahan', [
                'id_handler' => $laporan->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
