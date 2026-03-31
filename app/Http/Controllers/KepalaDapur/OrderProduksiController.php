<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\OrderProduksi;
use App\Models\OrderProduksiDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderProduksiController extends Controller
{
    public function index(Request $request)
    {
        $idDapur = session('id_dapur');

        if (!$idDapur) {
            abort(403, 'Unauthorized');
        }

        $statusFilter = $request->get('status', 'all');

        $query = OrderProduksi::where('id_dapur', $idDapur)
            ->with(['transaksiDapur.createdBy', 'transaksiDapur.detailTransaksiDapur.menuMakanan', 'distribusiOrder'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(15);

        $stats = [
            'stok_kurang'   => OrderProduksi::where('id_dapur', $idDapur)->where('status', 'stok_kurang')->count(),
            'belum_dibuat'  => OrderProduksi::where('id_dapur', $idDapur)->where('status', 'belum_dibuat')->count(),
            'sedang_dibuat' => OrderProduksi::where('id_dapur', $idDapur)->where('status', 'sedang_dibuat')->count(),
            'selesai'       => OrderProduksi::where('id_dapur', $idDapur)->where('status', 'selesai')->count(),
        ];

        return view('kepaladapur.order_produksi.index', compact('orders', 'idDapur', 'statusFilter', 'stats'));
    }

    public function show(OrderProduksi $order)
    {
        $idDapur = session('id_dapur');

        if (!$idDapur || $order->id_dapur !== $idDapur) {
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

        $stockData = [];
        foreach ($stockCheck["ingredients_summary"] as $item) {
            $stockData[$item["id_template_item"]] = [
                "stock_aktual" => $item["available"],
                "stock_tersedia" => $item["effective_available"],
                "sufficient" => $item["sufficient"],
            ];
        }

        return view(
            "kepaladapur.order_produksi.show",
            compact(
                "order",
                "transaksi",
                "bahanKebutuhan",
                "bahanBesar",
                "bahanKecil",
                "stockData",
                "shortages",
                "idDapur"
            )
        );
    }

    public function updateStatus(Request $request, OrderProduksi $order)
    {
        $idDapur = session('id_dapur');

        if (!$idDapur || $order->id_dapur !== $idDapur) {
            abort(403, 'Unauthorized');
        }

        if ($order->status === 'stok_kurang') {
            return redirect()->route('kepala-dapur.order-produksi.index')
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
