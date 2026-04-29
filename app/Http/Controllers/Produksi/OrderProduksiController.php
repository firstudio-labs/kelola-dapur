<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\OrderProduksi;
use App\Models\Produksi;
use App\Models\OrderProduksiDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderProduksiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $produksi = Produksi::whereHas('userRole', function ($q) use ($user) {
            $q->where('id_user', $user->id_user);
        })->first();

        if (!$produksi) {
            abort(403, 'Unauthorized');
        }

        $statusFilter = $request->get('status', 'all');

        $query = OrderProduksi::where('id_dapur', $produksi->id_dapur)
            ->with(['transaksiDapur.createdBy', 'transaksiDapur.detailTransaksiDapur.menuMakanan', 'distribusiOrder'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(15);

        $stats = [
            'stok_kurang'   => OrderProduksi::where('id_dapur', $produksi->id_dapur)->where('status', 'stok_kurang')->count(),
            'belum_dibuat'  => OrderProduksi::where('id_dapur', $produksi->id_dapur)->where('status', 'belum_dibuat')->count(),
            'sedang_dibuat' => OrderProduksi::where('id_dapur', $produksi->id_dapur)->where('status', 'sedang_dibuat')->count(),
            'selesai'       => OrderProduksi::where('id_dapur', $produksi->id_dapur)->where('status', 'selesai')->count(),
        ];

        return view('produksi.order.index', compact('orders', 'produksi', 'statusFilter', 'stats'));
    }

    public function show(OrderProduksi $order)
    {
        $user = Auth::user();

        $produksi = Produksi::whereHas("userRole", function ($q) use ($user) {
            $q->where("id_user", $user->id_user);
        })->first();

        if (!$produksi || $order->id_dapur !== $produksi->id_dapur) {
            abort(403, "Unauthorized");
        }

        $transaksi = $order->transaksiDapur;
        $transaksi->load([
            "detailTransaksiDapur.menuMakanan.bahanMenu.templateItem",
            "dapur",
            "createdBy",
            "laporanKekuranganStock.templateItem",
        ]);

        $order->load(['dokumentasi', 'distribusiOrder.dokumentasi']);

        $bahanKebutuhan = $transaksi->calculateIngredientNeeds();
        $bahanBesar = $transaksi->calculateIngredientNeedsByType("besar");
        $bahanKecil = $transaksi->calculateIngredientNeedsByType("kecil");

        $stockCheck = $transaksi->checkStockWithReservations();
        $shortages = $stockCheck["shortages"];

        $stockItems = \App\Models\StockItem::where('id_dapur', $produksi->id_dapur)->get()->keyBy('id_template_item');

        $stockData = [];
        foreach ($stockCheck["ingredients_summary"] as $item) {
            $idTemplate = $item["id_template_item"];
            $stockItem = $stockItems->get($idTemplate);

            $stockData[$idTemplate] = [
                "stock_aktual" => $item["available"],
                "stock_tersedia" => $item["effective_available"],
                "sufficient" => $item["sufficient"],
                "konversi_nilai" => $stockItem ? $stockItem->konversi_nilai : null,
                "konversi_satuan" => $stockItem ? $stockItem->konversi_satuan : null,
            ];
        }

        return view(
            "produksi.order.show",
            compact(
                "order",
                "transaksi",
                "bahanKebutuhan",
                "bahanBesar",
                "bahanKecil",
                "stockData",
                "shortages"
            )
        );
    }

    public function updateStatus(Request $request, OrderProduksi $order)
    {
        $user = Auth::user();

        $produksi = Produksi::whereHas('userRole', function ($q) use ($user) {
            $q->where('id_user', $user->id_user);
        })->first();

        if (!$produksi || $order->id_dapur !== $produksi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($order->status === 'stok_kurang') {
            return redirect()->route('produksi.order.index')
                ->with('error', 'Order ini tidak dapat diperbarui karena stok masih kurang. Tunggu hingga stok tercukupi.');
        }

        $request->validate([
            'status'  => 'required|in:belum_dibuat,sedang_dibuat,selesai',
            'catatan' => 'nullable|string|max:500',
            'dokumentasi.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $statusOrder = [
            'belum_dibuat'  => 1,
            'sedang_dibuat' => 2,
            'selesai'       => 3,
        ];

        $currentRank = $statusOrder[$order->status] ?? 0;
        $newRank     = $statusOrder[$request->status] ?? 0;

        if ($newRank < $currentRank) {
            return redirect()->back()
                ->with('error', 'Status tidak bisa dikembalikan ke tahap sebelumnya.');
        }

        if ($newRank > $currentRank + 1) {
            return redirect()->back()
                ->with('error', 'Status harus diubah secara berurutan (contoh: Belum Dibuat harus ke Sedang Dibuat terlebih dahulu).');
        }

        if ($request->status === 'selesai' && !$request->hasFile('dokumentasi')) {
            return redirect()->back()
                ->with('error', 'Harap unggah minimal 1 foto dokumentasi untuk status Selesai.');
        }

        DB::beginTransaction();
        try {
            if ($request->status === 'sedang_dibuat' && $order->status === 'belum_dibuat') {
                $transaksi   = $order->transaksiDapur;
                $deductResult = $transaksi->deductStockForProduction();

                if (!$deductResult['success']) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Gagal mengurangi stok: ' . $deductResult['message'] . '. Pastikan stok mencukupi.');
                }
            }

            $order->status  = $request->status;
            $order->catatan = $request->catatan;
            $order->save();

            if ($request->status === 'selesai' && $request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('produksi/dokumentasi', $filename, 'public');

                    OrderProduksiDokumentasi::create([
                        'id_order' => $order->id_order,
                        'path_gambar' => $path,
                    ]);
                }

                $orderDistribusi = \App\Models\OrderDistribusi::firstOrCreate(
                    ['id_order' => $order->id_order],
                    [
                        'id_dapur' => $order->id_dapur,
                        'status'   => \App\Models\OrderDistribusi::STATUS_BELUM_DIKIRIM,
                    ]
                );

                if ($orderDistribusi->wasRecentlyCreated) {
                    $penerimaMbgList = \App\Models\PenerimaMbg::where('id_dapur', $order->id_dapur)
                        ->where('status_approval', 'approved')
                        ->get();

                    foreach ($penerimaMbgList as $penerima) {
                        $pPorsi = $penerima->jumlah_porsi;

                        \App\Models\OrderDistribusiDetail::firstOrCreate(
                            [
                                'id_distribusi' => $orderDistribusi->id_distribusi,
                                'id_penerima'   => $penerima->id_penerima,
                            ],
                            [
                                'porsi_besar'     => $pPorsi,
                                'porsi_kecil'     => $pPorsi,
                                'jumlah_diterima' => $pPorsi * 2,
                                'status'          => \App\Models\OrderDistribusiDetail::STATUS_BELUM_DIKIRIM,
                            ]
                        );
                    }
                }
            } 

            DB::commit();

            return redirect()->back()
                ->with('success', 'Status order berhasil diperbarui menjadi "' . OrderProduksi::statusLabel()[$request->status] . '".');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order produksi status', [
                'id_order' => $order->id_order,
                'error'    => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
