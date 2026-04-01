<?php

namespace App\Http\Controllers\AdminGudang;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGudangController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin_gudang']);
    }

    public function dashboard(Request $request, Dapur $dapur)
    {
        
        $user = Auth::user();

        if (!$user->isAdminGudang($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini untuk dapur ini');
        }

        $adminGudang = $user->userRole()->where('role_type', 'admin_gudang')
            ->where('id_dapur', $dapur->id_dapur)
            ->first()
            ->adminGudang;

        $dashboardData = [
            'user' => $user,
            'dapur' => $dapur,
            'role' => 'admin_gudang',
            'adminGudang' => $adminGudang,
            'myRequests' => $adminGudang
                ? $adminGudang->approvalStockItems()->latest()->take(5)->get()
                : collect(),
            'pendingRequestsCount' => $adminGudang
                ? $adminGudang->approvalStockItems()->where('status', 'pending')->count()
                : 0,
            'totalStock' => $this->getTotalStockForDapur($dapur),
            'lowStockItems' => $this->getLowStockItemsForDapur($dapur),
            'pendingShortagesCount' => $this->getPendingShortagesCountForDapur($dapur),
            'recentShortages' => $this->getRecentShortagesForDapur($dapur),
        ];

        return view('admingudang.dashboard.index', $dashboardData);
    }

    private function getTotalStockForDapur(Dapur $dapur)
    {
        return \App\Models\StockItem::where('id_dapur', $dapur->id_dapur)->count();
    }

    private function getLowStockItemsForDapur(Dapur $dapur)
    {
        return \App\Models\StockItem::where('id_dapur', $dapur->id_dapur)
            ->where('jumlah', '<=', 10)
            ->count();
    }

    private function getPendingShortagesCountForDapur(Dapur $dapur)
    {
        return \App\Models\LaporanKekuranganStock::whereHas('transaksiDapur', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur);
            })
            ->where('status', 'pending')
            ->count();
    }

    private function getRecentShortagesForDapur(Dapur $dapur)
    {
        return \App\Models\LaporanKekuranganStock::whereHas('transaksiDapur', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur);
            })
            ->with(['templateItem', 'transaksiDapur'])
            ->latest('created_at')
            ->take(5)
            ->get();
    }
}
