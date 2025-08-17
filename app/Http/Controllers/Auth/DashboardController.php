<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Dapur;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Cek role pengguna
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Ambil daftar dapur yang dapat diakses
        $accessibleDapur = $this->getUserAccessibleDapur($user);

        if ($accessibleDapur->isEmpty()) {
            return view('dashboard.no-access', [
                'user' => $user
            ]);
        }

        // Jika hanya satu dapur, arahkan langsung ke dashboard role yang sesuai
        if ($accessibleDapur->count() === 1) {
            $dapur = $accessibleDapur->first();
            return $this->redirectToRoleDashboard($user, $dapur);
        }

        // Jika lebih dari satu dapur, tampilkan halaman pemilihan dapur
        return view('dashboard.select-dapur', [
            'user' => $user,
            'dapurList' => $accessibleDapur,
            'userRoles' => $this->getUserRolesForDapur($user, $accessibleDapur)
        ]);
    }

    public function switchDapur(Request $request, Dapur $dapur)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isSuperAdmin() && !$this->userHasAccessToDapur($user, $dapur)) {
            abort(403, 'Anda tidak memiliki akses ke dapur ini');
        }

        return $this->redirectToRoleDashboard($user, $dapur);
    }

    private function getUserAccessibleDapur(User $user)
    {
        return $user->accessibleDapur()->get();
    }

    private function getUserRolesForDapur(User $user, $dapurList)
    {
        $roles = [];
        foreach ($dapurList as $dapur) {
            $roles[$dapur->id_dapur] = $user->getUserRole($dapur->id_dapur);
        }
        return $roles;
    }

    private function userHasAccessToDapur(User $user, Dapur $dapur): bool
    {
        return $user->isKepalaDapur($dapur->id_dapur) ||
            $user->isAdminGudang($dapur->id_dapur) ||
            $user->isAhliGizi($dapur->id_dapur);
    }

    private function redirectToRoleDashboard(User $user, Dapur $dapur)
    {
        $userRole = $user->getUserRole($dapur->id_dapur);

        switch ($userRole) {
            case 'kepala_dapur':
                return redirect()->route('kepala-dapur.dashboard', ['dapur' => $dapur->id_dapur]);
            case 'admin_gudang':
                return redirect()->route('admin-gudang.dashboard', ['dapur' => $dapur->id_dapur]);
            case 'ahli_gizi':
                return redirect()->route('ahli-gizi.dashboard', ['dapur' => $dapur->id_dapur]);
            default:
                abort(403, 'Role tidak valid untuk dapur ini');
        }
    }
}
