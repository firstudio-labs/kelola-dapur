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
        
        if (!auth()->check() && !session('super_admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session('super_admin_logged_in')) {
            
            if ($this->validateSuperAdminSession($request)) {
                return $next($request);
            } else {
                
                session()->flush();
                return redirect()->route('login')->with('error', 'Sesi Super Admin tidak valid. Silakan login kembali.');
            }
        }

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

    private function validateSuperAdminSession(Request $request): bool
    {
        
        if (!session('is_super_admin') || !session('user_id') === 'super_admin') {
            return false;
        }

        return true;
    }
}
