<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Dapur;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (session('role_type') === 'super_admin') {
            return $next($request);
        }

        $user = Auth::user();
        $roleType = session('role_type');
        $idDapur = session('id_dapur');

        if (!$roleType || !$idDapur) {
            Log::warning('User access blocked - No role or dapur in session', [
                'user_id' => $user ? $user->id_user : null,
                'role_type' => $roleType,
                'id_dapur' => $idDapur,
                'url' => $request->url(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Session tidak valid. Silakan login kembali.');
        }

        $dapur = Dapur::find($idDapur);
        if (!$dapur) {
            Log::error('Dapur not found', [
                'user_id' => $user->id_user,
                'id_dapur' => $idDapur,
            ]);

            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Dapur tidak ditemukan. Silakan hubungi administrator.');
        }

        $this->updateSubscriptionSession($request, $dapur);

        $isSubscriptionActive = $dapur->isActive();
        $currentRoute = $request->route()->getName();

        Log::info('Subscription check', [
            'user_id' => $user->id_user,
            'role_type' => $roleType,
            'id_dapur' => $idDapur,
            'subscription_active' => $isSubscriptionActive,
            'subscription_status' => $dapur->getSubscriptionStatus(),
            'route' => $currentRoute,
        ]);

        switch ($roleType) {
            case 'kepala_dapur':
                return $this->handleKepalaDapurAccess($request, $next, $isSubscriptionActive, $currentRoute);

            case 'admin_gudang':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'ahli_gizi':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'produksi':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'distributor':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'akuntan':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'sarpas':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            default:
                Log::warning('Unknown role type', [
                    'user_id' => $user->id_user,
                    'role_type' => $roleType,
                ]);

                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Role tidak valid. Silakan hubungi administrator.');
        }
    }

    private function handleKepalaDapurAccess(Request $request, Closure $next, bool $isSubscriptionActive, string $currentRoute)
    {
        $allowedRoutes = [
            'kepala-dapur.dashboard',
            'kepala-dapur.subscription.index',
            'kepala-dapur.subscription.create',
            'kepala-dapur.subscription.choose-package',
            'kepala-dapur.subscription.process-payment',
            'kepala-dapur.subscription.calculate-price',
            'kepala-dapur.subscription.show',
            'kepala-dapur.subscription.cancel',
            'dashboard',
            'dashboard.switch-dapur',
            'logout',
            'kepala-dapur.edit-profil',
            'kepala-dapur.update-profil',
            'admin-gudang.dashboard',
            'ahli-gizi.dashboard'
        ];

        if ($isSubscriptionActive) {
            return $next($request);
        }

        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        return redirect()->route('kepala-dapur.dashboard', ['dapur' => session('id_dapur')])
            ->with('warning', 'Subscription Anda telah berakhir. Untuk mengakses fitur lengkap, silakan perpanjang subscription Anda.')
            ->with('subscription_expired', true);
    }

    private function handleStaffAccess(Request $request, Closure $next, bool $isSubscriptionActive, string $currentRoute, string $roleType, int $idDapur)
    {
        $allowedRoutes = [
            'dashboard',
            'dashboard.switch-dapur',
            'logout',
        ];

        if ($roleType === 'admin_gudang') {
            $allowedRoutes[] = 'admin-gudang.dashboard';
        } elseif ($roleType === 'ahli_gizi') {
            $allowedRoutes[] = 'ahli-gizi.dashboard';
        } elseif ($roleType === 'produksi') {
            $allowedRoutes[] = 'produksi.dashboard';
        } elseif ($roleType === 'distributor') {
            $allowedRoutes[] = 'distributor.dashboard';
        } elseif ($roleType === 'akuntan') {
            $allowedRoutes[] = 'akuntan.dashboard';
        } elseif ($roleType === 'sarpas') {
            $allowedRoutes[] = 'sarpas.dashboard';
        }

        if ($isSubscriptionActive) {
            return $next($request);
        }

        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        $dashboardRoute = match ($roleType) {
            'admin_gudang' => 'admin-gudang.dashboard',
            'ahli_gizi' => 'ahli-gizi.dashboard',
            'produksi' => 'produksi.dashboard',
            'distributor' => 'distributor.dashboard',
            'akuntan' => 'akuntan.dashboard',
            'sarpas' => 'sarpas.dashboard',
            default => 'login'
        };

        return redirect()->route($dashboardRoute, ['dapur' => $idDapur])
            ->with('warning', 'Akses terbatas karena subscription dapur telah berakhir. Hubungi Kepala Dapur untuk perpanjangan subscription.')
            ->with('subscription_expired', true);
    }

    private function updateSubscriptionSession(Request $request, Dapur $dapur)
    {
        $subscriptionStatus = $dapur->getSubscriptionStatus();
        $isActive = $dapur->isActive();

        $request->session()->put('dapur_status', $dapur->status);
        $request->session()->put('subscription_end', $dapur->subscription_end);
        $request->session()->put('subscription_status', $subscriptionStatus);
        $request->session()->put('is_subscription_active', $isActive);

        if ($subscriptionStatus === 'expiring_soon') {
            $daysLeft = now()->diffInDays($dapur->subscription_end);
            $request->session()->put('subscription_warning', true);
            $request->session()->put('subscription_days_left', $daysLeft);
        } else {
            $request->session()->forget(['subscription_warning', 'subscription_days_left']);
        }
    }
}
