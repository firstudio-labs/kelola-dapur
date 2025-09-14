<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Dapur;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil dapur dari route parameter
        $dapurId = $request->route('dapur');

        // Jika tidak ada dapur di route, skip middleware
        if (!$dapurId) {
            return $next($request);
        }

        // Ambil data dapur
        $dapur = Dapur::find($dapurId);

        if (!$dapur) {
            abort(404, 'Dapur tidak ditemukan');
        }

        // Cek status subscription
        $subscriptionStatus = $dapur->getSubscriptionStatus();

        // Jika subscription aktif, lanjutkan request
        if ($subscriptionStatus === 'active') {
            return $next($request);
        }

        // Jika subscription tidak aktif, cek apakah route yang diakses diizinkan
        $allowedRoutes = [
            'kepala-dapur.dashboard',
            'kepala-dapur.subscription.*',
            'admin-gudang.dashboard',
            'ahli-gizi.dashboard',
            'dashboard',
            'logout',
            'kepala-dapur.edit-profil',
            'kepala-dapur.update-profil'
        ];

        $currentRoute = $request->route()->getName();

        // Cek apakah route saat ini diizinkan
        foreach ($allowedRoutes as $allowedRoute) {
            if (str_contains($allowedRoute, '*')) {
                $pattern = str_replace('*', '', $allowedRoute);
                if (str_starts_with($currentRoute, $pattern)) {
                    return $next($request);
                }
            } else {
                if ($currentRoute === $allowedRoute) {
                    return $next($request);
                }
            }
        }

        // Jika route tidak diizinkan, redirect ke dashboard dengan pesan
        $message = $this->getSubscriptionMessage($subscriptionStatus);

        return redirect()
            ->route('kepala-dapur.dashboard', $dapurId)
            ->with('subscription_warning', $message);
    }

    /**
     * Get message based on subscription status
     */
    private function getSubscriptionMessage(string $status): string
    {
        switch ($status) {
            case 'no_subscription':
                return 'Dapur belum memiliki subscription aktif. Silakan lakukan subscription untuk mengakses semua fitur.';
            case 'expired':
                return 'Subscription dapur telah berakhir. Silakan perpanjang subscription untuk melanjutkan menggunakan semua fitur.';
            case 'expiring_soon':
                return 'Subscription dapur akan berakhir dalam 7 hari. Silakan perpanjang subscription Anda.';
            default:
                return 'Subscription tidak aktif. Silakan hubungi administrator.';
        }
    }
}
