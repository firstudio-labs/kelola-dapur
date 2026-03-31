<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\OrderDistribusi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dapur = $user->userRole->dapur;
        $distributor = $user->distributor;
        
        $today = Carbon::today();
        
        $todayDeliveries = OrderDistribusi::whereHas('orderProduksi.transaksiDapur', function($query) use ($dapur, $today) {
            $query->where('id_dapur', $dapur->id_dapur)
                  ->whereDate('tanggal_transaksi', $today);
        })
        ->with([
            'orderProduksi.transaksiDapur', 
            'details'
        ])
        ->get();

        $totalOrdersToday = $todayDeliveries->count();
        $ordersCompletedToday = $todayDeliveries->where('status', 'sudah_dikirim')->count();
        $ordersPendingToday = $todayDeliveries->whereIn('status', ['belum_dikirim', 'sedang_dikirim'])->count();

        $totalPendingAllTime = OrderDistribusi::whereIn('status', ['belum_dikirim', 'sedang_dikirim'])
            ->whereHas('orderProduksi.transaksiDapur', function($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur);
            })
            ->count();

        return view('distributor.dashboard', compact(
            'user', 
            'dapur', 
            'distributor', 
            'todayDeliveries', 
            'totalOrdersToday', 
            'ordersCompletedToday', 
            'ordersPendingToday',
            'totalPendingAllTime'
        ));
    }
}
