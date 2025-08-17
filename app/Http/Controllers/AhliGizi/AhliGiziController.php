<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use App\Models\MenuMakanan;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AhliGiziController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli_gizi']);
    }

    public function dashboard(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $userRole = $user->userRole()->where('role_type', 'ahli_gizi')->first();

        if (!$userRole || !$userRole->id_dapur) {
            abort(403, 'Anda tidak memiliki akses ke dapur sebagai ahli gizi');
        }

        $dapur = Dapur::findOrFail($userRole->id_dapur);

        if (!$user->isAhliGizi($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini untuk dapur ini');
        }

        $dashboardData = [
            'user' => $user,
            'dapur' => $dapur,
            'role' => 'ahli_gizi',
            'totalMenus' => $this->getTotalMenus(),
            'activeMenus' => $this->getActiveMenus(),
            'inactiveMenus' => $this->getInactiveMenus(),
            'totalTransactions' => $this->getTotalTransactionsForDapur($dapur),
            'recentMenus' => $this->getRecentMenus(),
        ];

        return view('ahligizi.dashboard.index', $dashboardData);
    }

    private function getTotalMenus()
    {
        return MenuMakanan::count();
    }

    private function getActiveMenus()
    {
        return MenuMakanan::where('is_active', true)->count();
    }

    private function getInactiveMenus()
    {
        return MenuMakanan::where('is_active', false)->count();
    }

    private function getTotalTransactionsForDapur(Dapur $dapur)
    {
        return $dapur->transaksiDapur()->count();
    }

    private function getRecentMenus()
    {
        return MenuMakanan::orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    }
}
