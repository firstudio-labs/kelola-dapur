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
        /** @var User $user */
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
            'myRequests' => $adminGudang
                ? $adminGudang->approvalStockItems()->latest()->take(5)->get()
                : collect(),
            'totalStock' => $this->getTotalStockForDapur($dapur),
            'lowStockItems' => $this->getLowStockItemsForDapur($dapur),
            'recentStockMovements' => $this->getRecentStockMovementsForDapur($dapur),
        ];

        return view('admingudang.dashboard.index', $dashboardData);
    }

    private function getTotalStockForDapur(Dapur $dapur)
    {
        return 0;
    }

    private function getLowStockItemsForDapur(Dapur $dapur)
    {
        return collect();
    }

    private function getRecentStockMovementsForDapur(Dapur $dapur)
    {
        return collect();
    }
}
