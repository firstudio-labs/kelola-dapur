<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        /** @var User $user */
        $user = auth()->user();

        // Validasi role
        $validRoles = ['super_admin', 'kepala_dapur', 'admin_gudang', 'ahli_gizi'];
        if (!in_array($role, $validRoles)) {
            throw new \InvalidArgumentException("Invalid role: {$role}");
        }

        // Super admin selalu diizinkan
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Cek role dengan lebih efisien
        if (!$this->hasRole($user, $role)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini');
        }

        return $next($request);
    }

    private function hasRole(User $user, string $role): bool
    {
        return match ($role) {
            'super_admin' => $user->isSuperAdmin(),
            'kepala_dapur' => $user->isKepalaDapur(),
            'admin_gudang' => $user->isAdminGudang(),
            'ahli_gizi' => $user->isAhliGizi(),
            default => false
        };
    }
}
