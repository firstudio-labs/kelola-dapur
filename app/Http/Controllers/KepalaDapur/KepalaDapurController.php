<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KepalaDapurController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:kepala_dapur']);
    }

    public function dashboard(Request $request, Dapur $dapur)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isKepalaDapur($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini untuk dapur ini');
        }

        $kepalaDapur = $user->userRole()->where('role_type', 'kepala_dapur')
            ->where('id_dapur', $dapur->id_dapur)
            ->first()
            ->kepalaDapur;

        $dashboardData = [
            'user' => $user,
            'dapur' => $dapur,
            'role' => 'kepala_dapur',
            'pendingApprovals' => $kepalaDapur
                ? $kepalaDapur->approvalStockItems()->where('status', 'pending')->count()
                : 0,
            'totalStock' => $this->getTotalStockForDapur($dapur),
            'lowStockItems' => $this->getLowStockItemsForDapur($dapur),
            'monthlyTransactions' => $this->getMonthlyTransactionsForDapur($dapur),
            'teamMembers' => $this->getTeamMembersForDapur($dapur),
        ];

        return view('kepaladapur.dashboard.index', $dashboardData);
    }

    private function getTotalStockForDapur(Dapur $dapur)
    {
        return 0;
    }

    private function getLowStockItemsForDapur(Dapur $dapur)
    {
        return collect();
    }

    private function getMonthlyTransactionsForDapur(Dapur $dapur)
    {
        return 0;
    }

    private function getTeamMembersForDapur(Dapur $dapur)
    {
        $kepalaDapur = $dapur->kepalaDapur()->with('user')->get();
        $adminGudang = $dapur->adminGudang()->with('user')->get();
        $ahliGizi = $dapur->ahliGizi()->with('user')->get();

        return [
            'kepala_dapur' => $kepalaDapur,
            'admin_gudang' => $adminGudang,
            'ahli_gizi' => $ahliGizi,
            'total' => $kepalaDapur->count() + $adminGudang->count() + $ahliGizi->count()
        ];
    }
}
