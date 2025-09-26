<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check() && !session('super_admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if it's super admin session
        if (session('super_admin_logged_in')) {
            // Additional security checks for super admin session
            if ($this->validateSuperAdminSession($request)) {
                return $next($request);
            } else {
                // Invalid super admin session, clear and redirect
                session()->flush();
                return redirect()->route('login')->with('error', 'Sesi Super Admin tidak valid. Silakan login kembali.');
            }
        }

        // Check regular user with super_admin role
        /** @var User $user */
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            Log::warning('Unauthorized super admin access attempt', [
                'user_id' => $user->id_user ?? null,
                'ip' => $request->ip(),
                'route' => $request->route()->getName(),
            ]);

            abort(403, 'Hanya Super Admin yang dapat mengakses halaman ini');
        }

        return $next($request);
    }

    /**
     * Validate super admin session for additional security
     */
    private function validateSuperAdminSession(Request $request): bool
    {
        // Basic session validation
        if (!session('is_super_admin') || !session('user_id') === 'super_admin') {
            return false;
        }

        // if (session('super_admin_ip') !== $request->ip()) {
        //     return false;
        // }

        // $loginTime = session('super_admin_login_time');
        // if ($loginTime && $loginTime->diffInHours(now()) > 8) {
        //     return false;
        // }

        return true;
    }
}
