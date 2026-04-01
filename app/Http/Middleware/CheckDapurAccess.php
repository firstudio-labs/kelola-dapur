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

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $validRoles = ['kepala_dapur', 'admin_gudang', 'ahli_gizi', 'penerima_mbg', 'produksi', 'distributor', 'mitra', 'akuntan'];
        foreach ($roles as $role) {
            if (!in_array($role, $validRoles)) {
                throw new \InvalidArgumentException("Invalid role: {$role}");
            }
        }

        $dapurId = $this->getDapurId($request);
        if (!$dapurId) {
            abort(400, 'ID Dapur tidak ditemukan');
        }

        $dapur = $this->getCachedDapur($dapurId);
        if (!$dapur) {
            abort(404, 'Dapur tidak ditemukan atau tidak aktif');
        }

        if (!$this->hasRoleAccess($user, $dapurId, $roles)) {
            abort(403, 'Anda tidak memiliki akses untuk dapur ini');
        }

        $request->merge([
            'current_dapur' => $dapur,
            'user_role' => $user->getUserRole($dapurId)
        ]);

        return $next($request);
    }

    private function getDapurId(Request $request): ?int
    {
        $dapurParam = $request->route('dapur');

        if ($dapurParam instanceof Dapur) {
            return $dapurParam->id_dapur;
        }

        if (is_numeric($dapurParam)) {
            return (int) $dapurParam;
        }

        $idDapurParam = $request->route('id_dapur');
        if (is_numeric($idDapurParam)) {
            return (int) $idDapurParam;
        }

        $queryDapur = $request->input('id_dapur') ?? $request->input('dapur');
        if (is_numeric($queryDapur)) {
            return (int) $queryDapur;
        }

        return null;
    }

    private function getCachedDapur(int $dapurId): ?Dapur
    {
        return cache()->remember(
            "dapur.{$dapurId}",
            300, 
            fn() => Dapur::where('id_dapur', $dapurId)->first()
        );
    }

    private function hasRoleAccess(User $user, int $dapurId, array $roles): bool
    {
        foreach ($roles as $role) {
            $hasAccess = match ($role) {
                'kepala_dapur' => $user->isKepalaDapur($dapurId),
                'admin_gudang' => $user->isAdminGudang($dapurId),
                'ahli_gizi' => $user->isAhliGizi($dapurId),
                'penerima_mbg' => $user->isPenerimaMbg($dapurId),
                'produksi' => $user->isProduksi($dapurId),
                'distributor' => $user->isDistributor($dapurId),
                'mitra' => $user->isMitraInDapur($dapurId),
                'akuntan' => $user->isAkuntan($dapurId),
                default => false
            };

            if ($hasAccess) {
                return true;
            }
        }

        return false;
    }
}
