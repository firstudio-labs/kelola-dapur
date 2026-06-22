<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ApprovalTransaksi;
use App\Models\ApprovalStockItem;
use App\Models\LaporanKekuranganStock;
use App\Models\MitraDapur;
use App\Models\KepalaDapur;
use App\Models\UserRole;

class KepalaDapurComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $idDapur = session('id_dapur');

        if (!$idDapur) {
            return;
        }

        try {
            $pendingTransaksiCount = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($idDapur) {
                $q->where('id_dapur', $idDapur);
            })->where('status', 'pending')->count();

            $pendingApprovalsCount = 0;
            $userRole = UserRole::where('id_user', $user->id_user)
                ->where('role_type', 'kepala_dapur')
                ->where('id_dapur', $idDapur)
                ->first();

            if ($userRole) {
                $kepalaDapur = KepalaDapur::where('id_user_role', $userRole->id_user_role)->first();
                if ($kepalaDapur) {
                    $pendingApprovalsCount = ApprovalStockItem::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
                        ->where('status', 'pending')
                        ->count();
                }
            }

            $pendingShortageCount = \App\Models\TransaksiDapur::where('id_dapur', $idDapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'pending');
                })->count();
            $pendingMitraCount = MitraDapur::where('id_dapur', $idDapur)
                ->where('status', 'pending')
                ->count();

            $view->with([
                'pendingTransaksiCount'  => $pendingTransaksiCount,
                'pendingApprovalsCount'  => $pendingApprovalsCount,
                'pendingShortageCount'   => $pendingShortageCount,
                'pendingMitraCount'      => $pendingMitraCount,
            ]);
        } catch (\Exception $e) {
        }
    }
}
