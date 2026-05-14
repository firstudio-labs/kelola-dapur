<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\OrderDistribusi;
use App\Models\OrderDistribusiDetail;
use App\Models\OrderDistribusiDetailDokumentasi;
use App\Models\OrderDistribusiDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderDistribusiController extends Controller
{
    public function index(Request $request)
    {
        $idDapur = session('id_dapur');
        if (!$idDapur) abort(403, 'Unauthorized');

        $statusFilter = $request->get('status', 'all');

        $query = OrderDistribusi::select('order_distribusi.*')
            ->join('order_produksi', 'order_distribusi.id_order', '=', 'order_produksi.id_order')
            ->join('transaksi_dapur', 'order_produksi.id_transaksi', '=', 'transaksi_dapur.id_transaksi')
            ->with([
                'orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
                'orderProduksi.transaksiDapur.createdBy',
                'dokumentasi',
                'details',
            ])
            ->where('order_distribusi.id_dapur', $idDapur);

        if ($statusFilter !== 'all') {
            $query->where('order_distribusi.status', $statusFilter);
        }

        $query->orderByRaw("ABS(DATEDIFF(transaksi_dapur.tanggal_transaksi, CURRENT_DATE)) ASC")
              ->orderByRaw("FIELD(order_distribusi.status, 'belum_dikirim', 'sedang_dikirim', 'sudah_dikirim')");

        $orders = $query->paginate(15);

        return view('kepaladapur.order_distribusi.index', compact('orders', 'idDapur', 'statusFilter'));
    }

    public function show(OrderDistribusi $order)
    {
        $idDapur = session('id_dapur');
        if (!$idDapur || $order->id_dapur !== $idDapur) {
            abort(403, 'Unauthorized');
        }

        $order->load([
            'orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
            'orderProduksi.transaksiDapur.createdBy',
            'orderProduksi.transaksiDapur.dapur',
            'orderProduksi.dokumentasi',
            'dokumentasi',
            'details.penerimaMbg.userRole.user',
            'details.dokumentasi',
        ]);

        $transaksi      = $order->orderProduksi->transaksiDapur;
        $bahanKebutuhan = $transaksi->calculateIngredientNeeds();
        $bahanBesar     = $transaksi->calculateIngredientNeedsByType('besar');
        $bahanKecil     = $transaksi->calculateIngredientNeedsByType('kecil');

        $totalPorsiHarusDikirim = $order->details->sum('jumlah_diterima');
        $totalPorsiTerkirim     = $order->details->where('status', OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM)->sum('jumlah_diterima');

        return view('kepaladapur.order_distribusi.show', compact(
            'order', 'transaksi',
            'bahanKebutuhan', 'bahanBesar', 'bahanKecil',
            'totalPorsiHarusDikirim', 'totalPorsiTerkirim', 'idDapur'
        ));
    }

    public function updateDetailStatus(Request $request, OrderDistribusi $order, OrderDistribusiDetail $detail)
    {
        $idDapur = session('id_dapur');
        if (!$idDapur || $order->id_dapur !== $idDapur) {
            abort(403, 'Unauthorized');
        }

        if ($detail->id_distribusi !== $order->id_distribusi) {
            abort(404);
        }

        if ($detail->status === OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM) {
            return redirect()->back()->with('error', 'Pengiriman ke penerima ini sudah selesai dan tidak dapat diubah.');
        }

        $statusOrder = [
            'belum_dikirim'  => 1,
            'sedang_dikirim' => 2,
            'sudah_dikirim'  => 3,
        ];

        $request->validate([
            'status'           => 'required|in:belum_dikirim,sedang_dikirim,sudah_dikirim',
            'catatan'          => 'nullable|string|max:1000',
            'porsi_besar'      => 'nullable|integer|min:0',
            'porsi_kecil'      => 'nullable|integer|min:0',
            'dokumentasi.*'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (($statusOrder[$request->status] ?? 0) < ($statusOrder[$detail->status] ?? 0)) {
            return redirect()->back()->with('error', 'Status tidak bisa dikembalikan ke tahap sebelumnya.');
        }

        if ($request->status === 'sudah_dikirim' && !$request->hasFile('dokumentasi')) {
            return redirect()->back()->with('error', 'Harap unggah minimal 1 foto bukti pengiriman untuk status Sudah Dikirim.');
        }

        DB::beginTransaction();
        try {
            $penerima = $detail->penerimaMbg;
            if (!$penerima) {
                return redirect()->back()->with('error', 'Data penerima tidak ditemukan.');
            }

            $maxPorsi = $penerima->jumlah_porsi;
            $pBesar = $request->porsi_besar ?? 0;
            $pKecil = $request->porsi_kecil ?? 0;

            if ($pBesar > $maxPorsi || $pKecil > $maxPorsi) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Jumlah porsi tidak boleh melebihi batas maksimal ({$maxPorsi} porsi).");
            }

            $detail->status  = $request->status;
            if ($request->status === OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM && !$detail->tanggal_dikirim) {
                $detail->tanggal_dikirim = now();
            }
            $detail->catatan = $request->catatan;
            
            if ($request->filled('porsi_besar') || $request->filled('porsi_kecil')) {
                $detail->porsi_besar = $request->porsi_besar ?? 0;
                $detail->porsi_kecil = $request->porsi_kecil ?? 0;
                $detail->jumlah_diterima = $detail->porsi_besar;
            }
            $detail->save();

            if ($request->hasFile('dokumentasi')) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                foreach ($request->file('dokumentasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'distribusi/detail-dokumentasi/' . $filename;
                    $img = $manager->read($file->getRealPath());
                    if ($img->width() > 1920) $img->scaleDown(width: 1920);
                    \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    OrderDistribusiDetailDokumentasi::create([
                        'id_detail'   => $detail->id_detail,
                        'path_gambar' => $path,
                    ]);
                }
            }

            $order->recalculateStatus();

            DB::commit();
            return redirect()->back()->with('success', 'Status pengiriman ke ' . ($detail->penerimaMbg->penanggung_jawab ?? 'penerima') . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update distribusi detail status', ['id_detail' => $detail->id_detail, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, OrderDistribusi $order)
    {
        $idDapur = session('id_dapur');
        if (!$idDapur || $order->id_dapur !== $idDapur) {
            abort(403, 'Unauthorized');
        }

        if ($order->status === OrderDistribusi::STATUS_SUDAH_DIKIRIM) {
            return redirect()->back()->with('error', 'Order sudah selesai dikirim dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'status'        => 'required|in:belum_dikirim,sedang_dikirim,sudah_dikirim',
            'catatan'       => 'nullable|string|max:1000',
            'dokumentasi.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $statusOrder = [
            'belum_dikirim'  => 1,
            'sedang_dikirim' => 2,
            'sudah_dikirim'  => 3,
        ];

        if (($statusOrder[$request->status] ?? 0) < ($statusOrder[$order->status] ?? 0)) {
            return redirect()->back()->with('error', 'Status tidak bisa dikembalikan ke tahap sebelumnya.');
        }

        if ($request->status === 'sudah_dikirim' && !$request->hasFile('dokumentasi')) {
            return redirect()->back()->with('error', 'Harap unggah minimal 1 foto bukti pengiriman untuk status Sudah Dikirim.');
        }

        DB::beginTransaction();
        try {
            $order->status  = $request->status;
            if ($request->status === OrderDistribusi::STATUS_SUDAH_DIKIRIM && !$order->tanggal_dikirim) {
                $order->tanggal_dikirim = now();
            }
            $order->catatan = $request->catatan;
            $order->save();

            if ($request->status === 'sudah_dikirim' && $request->hasFile('dokumentasi')) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                foreach ($request->file('dokumentasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.webp';
                    $path = 'distribusi/dokumentasi/' . $filename;
                    $img = $manager->read($file->getRealPath());
                    if ($img->width() > 1920) $img->scaleDown(width: 1920);
                    \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $img->toWebp(80));
                    OrderDistribusiDokumentasi::create([
                        'id_distribusi' => $order->id_distribusi,
                        'path_gambar'   => $path,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui menjadi "' . OrderDistribusi::statusLabel()[$request->status] . '".');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order distribusi status', ['id' => $order->id_distribusi, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
