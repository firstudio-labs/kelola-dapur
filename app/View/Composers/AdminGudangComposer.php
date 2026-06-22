<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiDapur;
use App\Models\ProduksiHandlerBahan;

class AdminGudangComposer
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
            $pendingShortageCount = TransaksiDapur::where('id_dapur', $idDapur)
                ->whereHas('laporanKekuranganStock', function ($q) {
                    $q->where('status', 'pending');
                })->count();

            $pendingLaporanStokCount = ProduksiHandlerBahan::whereHas('orderProduksi', function ($q) use ($idDapur) {
                $q->where('id_dapur', $idDapur);
            })->where('status', 'pending')->count();

            $view->with([
                'pendingShortageCount'    => $pendingShortageCount,
                'pendingLaporanStokCount' => $pendingLaporanStokCount,
            ]);
        } catch (\Exception $e) {
        }
    }
}
