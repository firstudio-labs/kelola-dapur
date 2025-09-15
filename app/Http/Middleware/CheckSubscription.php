<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Dapur;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for super admin
        if (session('role_type') === 'super_admin') {
            return $next($request);
        }

        // Get current user and session data
        $user = Auth::user();
        $roleType = session('role_type');
        $idDapur = session('id_dapur');

        // Redirect if no role or dapur
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

        // Refresh subscription status from database
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

        // Update session with fresh subscription data
        $this->updateSubscriptionSession($request, $dapur);

        $isSubscriptionActive = $dapur->isActive();
        $currentRoute = $request->route()->getName();

        // Log access attempt for monitoring
        Log::info('Subscription check', [
            'user_id' => $user->id_user,
            'role_type' => $roleType,
            'id_dapur' => $idDapur,
            'subscription_active' => $isSubscriptionActive,
            'subscription_status' => $dapur->getSubscriptionStatus(),
            'route' => $currentRoute,
        ]);

        // Handle different role types
        switch ($roleType) {
            case 'kepala_dapur':
                return $this->handleKepalaDapurAccess($request, $next, $isSubscriptionActive, $currentRoute);

            case 'admin_gudang':
                return $this->handleStaffAccess($request, $next, $isSubscriptionActive, $currentRoute, $roleType, $idDapur);

            case 'ahli_gizi':
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

    /**
     * Handle access for Kepala Dapur role
     */
    private function handleKepalaDapurAccess(Request $request, Closure $next, bool $isSubscriptionActive, string $currentRoute)
    {
        // Always allow access to dashboard and subscription routes
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
            // 'kepala-dapur.approval-transaksi.index',
            // 'kepala-dapur.approval-transaksi.show',
            // 'kepala-dapur.approval-transaksi.approve',
            // 'kepala-dapur.approval-transaksi.reject'
        ];

        // If subscription is active, allow all kepala dapur routes
        if ($isSubscriptionActive) {
            return $next($request);
        }

        // If subscription is not active, only allow specific routes
        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        // Block access and redirect to dashboard with message
        return redirect()->route('kepala-dapur.dashboard', ['dapur' => session('id_dapur')])
            ->with('warning', 'Subscription Anda telah berakhir. Untuk mengakses fitur lengkap, silakan perpanjang subscription Anda.')
            ->with('subscription_expired', true);
    }

    /**
     * Handle access for Admin Gudang and Ahli Gizi roles
     */
    private function handleStaffAccess(Request $request, Closure $next, bool $isSubscriptionActive, string $currentRoute, string $roleType, int $idDapur)
    {
        // Always allow access to basic routes
        $allowedRoutes = [
            'dashboard',
            'dashboard.switch-dapur',
            'logout',
        ];

        // Role-specific dashboard routes
        if ($roleType === 'admin_gudang') {
            $allowedRoutes[] = 'admin-gudang.dashboard';
        } elseif ($roleType === 'ahli_gizi') {
            $allowedRoutes[] = 'ahli-gizi.dashboard';
        }

        // If subscription is active, allow all routes for this role
        if ($isSubscriptionActive) {
            return $next($request);
        }

        // If subscription is not active, only allow dashboard access
        if (in_array($currentRoute, $allowedRoutes)) {
            return $next($request);
        }

        // Block access and redirect to appropriate dashboard
        $dashboardRoute = $roleType === 'admin_gudang'
            ? 'admin-gudang.dashboard'
            : 'ahli-gizi.dashboard';

        return redirect()->route($dashboardRoute, ['dapur' => $idDapur])
            ->with('warning', 'Akses terbatas karena subscription dapur telah berakhir. Hubungi Kepala Dapur untuk perpanjangan subscription.')
            ->with('subscription_expired', true);
    }

    /**
     * Update session with fresh subscription data
     */
    private function updateSubscriptionSession(Request $request, Dapur $dapur)
    {
        $subscriptionStatus = $dapur->getSubscriptionStatus();
        $isActive = $dapur->isActive();

        // Update session data
        $request->session()->put('dapur_status', $dapur->status);
        $request->session()->put('subscription_end', $dapur->subscription_end);
        $request->session()->put('subscription_status', $subscriptionStatus);
        $request->session()->put('is_subscription_active', $isActive);

        // Add subscription warning flags
        if ($subscriptionStatus === 'expiring_soon') {
            $daysLeft = now()->diffInDays($dapur->subscription_end);
            $request->session()->put('subscription_warning', true);
            $request->session()->put('subscription_days_left', $daysLeft);
        } else {
            $request->session()->forget(['subscription_warning', 'subscription_days_left']);
        }
    }
}
