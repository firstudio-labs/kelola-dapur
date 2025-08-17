<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        /** @var User $user */
        $user = auth()->user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses halaman ini');
        }

        return $next($request);
    }
}
