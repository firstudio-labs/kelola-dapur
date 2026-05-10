<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\OrderDistribusiDetail;

class LaporanAduanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->userRole || $user->userRole->role_type !== 'mitra') {
            abort(403, 'Unauthorized access');
        }

        $mitra = Mitra::where('id_user_role', $user->userRole->id_user_role)->first();
        if (!$mitra) {
            abort(403, 'Mitra profile not found');
        }

        $dapurApproved = $mitra->dapurApproved;
        $dapurIds = $dapurApproved->pluck('id_dapur')->toArray();
        
        $selectedDapurId = $request->input('dapur');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($selectedDapurId) {
            $dapurIds = array_intersect($dapurIds, [$selectedDapurId]);
        }

        $aduans = \App\Models\OrderProduksi::whereIn('id_dapur', $dapurIds)
            ->where(function($q) {
                $q->whereNotNull('ulasan')
                  ->orWhereHas('distribusiOrder', function($dq) {
                      $dq->whereNotNull('ulasan')
                        ->orWhereHas('details', function($dtq) {
                            $dtq->whereNotNull('kritik');
                        });
                  });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('ulasan', 'like', '%' . $search . '%')
                      ->orWhereHas('distribusiOrder', function($dq) use ($search) {
                          $dq->where('ulasan', 'like', '%' . $search . '%')
                            ->orWhereHas('details', function($dtq) use ($search) {
                                $dtq->where('kritik', 'like', '%' . $search . '%')
                                    ->orWhereHas('penerimaMbg.userRole.user', function($uq) use ($search) {
                                        $uq->where('nama', 'like', '%' . $search . '%');
                                    });
                            });
                      });
                });
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('updated_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('updated_at', '<=', $dateTo);
            })
            ->with([
                'transaksiDapur.dapur',
                'transaksiDapur.approvalTransaksi',
                'distribusiOrder.details.penerimaMbg.userRole.user'
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        $dapurIdsInAduan = $aduans->pluck('id_dapur')->unique()->toArray();
        
        $productionHeads = \App\Models\Produksi::whereIn('id_dapur', $dapurIdsInAduan)
            ->orderByRaw("CASE WHEN jabatan = 'Penanggung jawab' THEN 0 ELSE 1 END")
            ->get()
            ->groupBy('id_dapur')
            ->map(fn($group) => $group->first());
            
        $distributionHeads = \App\Models\Distributor::whereIn('id_dapur', $dapurIdsInAduan)
            ->orderByRaw("CASE WHEN jabatan = 'Penanggung jawab' THEN 0 ELSE 1 END")
            ->get()
            ->groupBy('id_dapur')
            ->map(fn($group) => $group->first());

        return view('mitra.laporan-aduan.index', compact(
            'aduans', 
            'dapurApproved', 
            'selectedDapurId', 
            'search', 
            'dateFrom', 
            'dateTo',
            'productionHeads',
            'distributionHeads'
        ));
    }
}
