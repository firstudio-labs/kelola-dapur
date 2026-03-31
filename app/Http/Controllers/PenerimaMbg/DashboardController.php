<?php

namespace App\Http\Controllers\PenerimaMbg;

use App\Http\Controllers\Controller;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)
            ->with('dapur')
            ->first();

        $today = now()->toDateString();
        $todayDeliveries = [];

        if ($penerima) {
            $todayDeliveries = \App\Models\OrderDistribusiDetail::where('id_penerima', $penerima->id_penerima)
                ->where('status', \App\Models\OrderDistribusiDetail::STATUS_SUDAH_DIKIRIM)
                ->whereHas('orderDistribusi.orderProduksi.transaksiDapur', function ($query) use ($today) {
                    $query->whereDate('tanggal_transaksi', $today);
                })
                ->with(['orderDistribusi.orderProduksi.transaksiDapur.detailTransaksiDapur.menuMakanan'])
                ->get();
        }

        return view('penerima_mbg.dashboard', compact('user', 'penerima', 'todayDeliveries'));
    }
}
