<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Dapur;
use App\Models\User;

class CheckDapurAccess
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        /** @var User $user */
        $user = auth()->user();

        // Super admin selalu diizinkan
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Validasi role yang diterima
        $validRoles = ['kepala_dapur', 'admin_gudang', 'ahli_gizi'];
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                throw new \InvalidArgumentException("Invalid role: {$role}");
            }
        }

        // Ambil dapur ID dengan prioritas yang konsisten
        $dapurId = $this->getDapurId($request);
        if (!$dapurId) {
            abort(400, 'ID Dapur tidak ditemukan');
        }

        // Cache dapur untuk menghindari query berulang
        $dapur = $this->getCachedDapur($dapurId);
        if (!$dapur) {
            abort(404, 'Dapur tidak ditemukan atau tidak aktif');
        }

        // Cek akses user
        if (!$this->hasRoleAccess($user, $dapurId, $roles)) {
            abort(403, 'Anda tidak memiliki akses untuk dapur ini');
        }

        // Tambahkan data ke request
        $request->merge([
            'current_dapur' => $dapur,
            'user_role' => $user->getUserRole($dapurId)
        ]);

        return $next($request);
    }

    private function getDapurId(Request $request): ?int
    {
        // Prioritas: route parameter > query parameter
        $dapurParam = $request->route('dapur');

        // Jika route parameter adalah instance model Dapur (route model binding)
        if ($dapurParam instanceof Dapur) {
            return $dapurParam->id_dapur;
        }

        // Jika route parameter adalah integer
        if (is_numeric($dapurParam)) {
            return (int) $dapurParam;
        }

        // Coba dari parameter lain
        $idDapurParam = $request->route('id_dapur');
        if (is_numeric($idDapurParam)) {
            return (int) $idDapurParam;
        }

        // Coba dari query string
        $queryDapur = $request->input('id_dapur') ?? $request->input('dapur');
        if (is_numeric($queryDapur)) {
            return (int) $queryDapur;
        }

        return null;
    }

    private function getCachedDapur(int $dapurId): ?Dapur
    {
        // Gunakan cache untuk menghindari query berulang dalam request yang sama
        return cache()->remember(
            "dapur.{$dapurId}",
            300, // 5 menit
            fn() => Dapur::where('id_dapur', $dapurId)->where('status', 'active')->first()
        );
    }

    private function hasRoleAccess(User $user, int $dapurId, array $roles): bool
    {
        foreach ($roles as $role) {
            $hasAccess = match ($role) {
                'kepala_dapur' => $user->isKepalaDapur($dapurId),
                'admin_gudang' => $user->isAdminGudang($dapurId),
                'ahli_gizi' => $user->isAhliGizi($dapurId),
                default => false
            };

            if ($hasAccess) {
                return true;
            }
        }

        return false;
    }
}
