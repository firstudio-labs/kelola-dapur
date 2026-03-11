<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\OrderDistribusi;
use App\Models\OrderDistribusiDokumentasi;
use App\Models\OrderProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private function getDistributor()
    {
        $user = Auth::user();
        return Distributor::whereHas('userRole', fn($q) => $q->where('id_user', $user->id_user))->first();
    }

    public function index(Request $request)
    {
        $distributor = $this->getDistributor();
        if (!$distributor) abort(403, 'Unauthorized');

        $statusFilter = $request->get('status', 'all');

        $query = OrderDistribusi::with([
                'orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
                'orderProduksi.transaksiDapur.createdBy',
                'dokumentasi',
            ])
            ->where('id_dapur', $distributor->id_dapur);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->latest()->paginate(15);

        return view('distribusi.order.index', compact('orders', 'statusFilter'));
    }

    public function show(OrderDistribusi $order)
    {
        $distributor = $this->getDistributor();
        if (!$distributor || $order->id_dapur !== $distributor->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $order->load([
            'orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan',
            'orderProduksi.transaksiDapur.createdBy',
            'orderProduksi.transaksiDapur.dapur',
            'orderProduksi.dokumentasi',
            'dokumentasi',
        ]);

        $transaksi = $order->orderProduksi->transaksiDapur;
        $bahanKebutuhan = $transaksi->calculateIngredientNeeds();
        $bahanBesar     = $transaksi->calculateIngredientNeedsByType('besar');
        $bahanKecil     = $transaksi->calculateIngredientNeedsByType('kecil');

        return view('distribusi.order.show', compact('order', 'transaksi', 'bahanKebutuhan', 'bahanBesar', 'bahanKecil'));
    }

    public function updateStatus(Request $request, OrderDistribusi $order)
    {
        $distributor = $this->getDistributor();
        if (!$distributor || $order->id_dapur !== $distributor->id_dapur) {
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
            $order->catatan = $request->catatan;
            $order->save();

            if ($request->status === 'sudah_dikirim' && $request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('distribusi/dokumentasi', $filename, 'public');
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
