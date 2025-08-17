<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Dapur;
use App\Models\User;

class DapurRoleGuard
{
    private array $permissions = [
        'stock.view' => ['kepala_dapur', 'admin_gudang', 'ahli_gizi'],
        'stock.request' => ['admin_gudang'],
        'stock.approve' => ['kepala_dapur'],

        'menu.view' => ['kepala_dapur', 'admin_gudang', 'ahli_gizi'],
        'menu.create' => ['ahli_gizi'],
        'menu.edit' => ['ahli_gizi'],

        'transaction.view' => ['kepala_dapur', 'ahli_gizi'],
        'transaction.create' => ['ahli_gizi'],
        'transaction.approve' => ['kepala_dapur'],

        'user.manage' => ['kepala_dapur'],

        'report.view' => ['kepala_dapur', 'admin_gudang', 'ahli_gizi'],
        'report.stock' => ['kepala_dapur', 'admin_gudang'],

        'view' => ['kepala_dapur', 'admin_gudang', 'ahli_gizi'],
    ];

    public function handle(Request $request, Closure $next, string $action = 'view'): Response
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

        // Validasi action
        if (!isset($this->permissions[$action])) {
            throw new \InvalidArgumentException("Invalid action: {$action}");
        }

        // Ambil dapur ID
        $dapurId = $this->getDapurId($request);
        if (!$dapurId) {
            abort(400, 'ID Dapur tidak ditemukan');
        }

        // Cache dapur
        $dapur = $this->getCachedDapur($dapurId);
        if (!$dapur) {
            abort(404, 'Dapur tidak ditemukan atau tidak aktif');
        }

        // Cek permission
        if (!$this->checkPermission($user, $dapurId, $action)) {
            abort(403, 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        // Tambahkan data ke request
        $request->merge([
            'current_dapur' => $dapur,
            'user_role' => $user->getUserRole($dapurId),
            'allowed_actions' => $this->getAllowedActions($user, $dapurId)
        ]);

        return $next($request);
    }

    private function getDapurId(Request $request): ?int
    {
        return $request->route('dapur')
            ?? $request->route('id_dapur')
            ?? $request->input('id_dapur')
            ?? $request->input('dapur');
    }

    private function getCachedDapur(int $dapurId): ?Dapur
    {
        return cache()->remember(
            "dapur.{$dapurId}",
            300,
            fn() => Dapur::where('id_dapur', $dapurId)->where('status', 'active')->first()
        );
    }

    private function checkPermission(User $user, int $dapurId, string $action): bool
    {
        $allowedRoles = $this->permissions[$action] ?? [];
        $userRole = $user->getUserRole($dapurId);

        return in_array($userRole, $allowedRoles);
    }

    private function getAllowedActions(User $user, int $dapurId): array
    {
        $userRole = $user->getUserRole($dapurId);
        $allowedActions = [];

        foreach ($this->permissions as $action => $roles) {
            if (in_array($userRole, $roles)) {
                $allowedActions[] = $action;
            }
        }

        return $allowedActions;
    }
}
