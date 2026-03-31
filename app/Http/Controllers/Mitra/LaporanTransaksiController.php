<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\ApprovalTransaksi;
use App\Models\StockSnapshot;
use App\Models\StockItem;
use App\Models\TransaksiDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isMitra()) {
            abort(403, 'Unauthorized access.');
        }

        $mitra = $user->mitra;
        $approvedDapurIds = $mitra->dapurApproved()->pluck('dapur.id_dapur'); // The pivot uses id_dapur

        $query = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
            $q->whereIn('id_dapur', $approvedDapurIds);
        })->with([
            'transaksiDapur.createdBy',
            'transaksiDapur.detailTransaksiDapur.menuMakanan',
            'transaksiDapur.dapur'
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('transaksiDapur', function ($q) use ($search) {
                $q->where('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('createdBy', function ($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dapur', function ($q) use ($search) {
                        $q->where('nama_dapur', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('dapur')) {
             $query->whereHas('transaksiDapur', function ($q) use ($request) {
                $q->where('id_dapur', $request->dapur);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereHas('transaksiDapur', function ($q) use ($request) {
                $q->whereDate('tanggal_transaksi', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('transaksiDapur', function ($q) use ($request) {
                $q->whereDate('tanggal_transaksi', '<=', $request->date_to);
            });
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            if ($sort === 'created_by') {
                $query->join('transaksi_dapur', 'approval_transaksi.id_transaksi', '=', 'transaksi_dapur.id_transaksi')
                    ->join('users', 'transaksi_dapur.created_by', '=', 'users.id_user')
                    ->orderBy('users.nama', 'asc')
                    ->select('approval_transaksi.*');
            } elseif ($sort === 'tanggal_transaksi') {
                $query->join('transaksi_dapur', 'approval_transaksi.id_transaksi', '=', 'transaksi_dapur.id_transaksi')
                    ->orderBy('transaksi_dapur.tanggal_transaksi', 'desc')
                    ->select('approval_transaksi.*');
            } else {
                $query->orderBy($sort, 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $approvals = $query->paginate(10)->appends($request->query());

        // For Dapur filter dropdown
        $dapurs = $mitra->dapurApproved;

        $stats = [
            'total' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
                $q->whereIn('id_dapur', $approvedDapurIds);
            })->count(),
            'pending' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
                $q->whereIn('id_dapur', $approvedDapurIds);
            })->where('status', 'pending')->count(),
            'approved' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
                $q->whereIn('id_dapur', $approvedDapurIds);
            })->where('status', 'approved')->count(),
            'rejected' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
                $q->whereIn('id_dapur', $approvedDapurIds);
            })->where('status', 'rejected')->count(),
        ];

        return view('mitra.laporan-transaksi.index', compact('approvals', 'stats', 'dapurs'));
    }

    public function show(Request $request, $approvalId)
    {
        $user = Auth::user();

        if (!$user->isMitra()) {
            abort(403, 'Unauthorized access.');
        }

        $mitra = $user->mitra;
        $approvedDapurIds = $mitra->dapurApproved()->pluck('dapur.id_dapur');

        $approval = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($approvedDapurIds) {
            $q->whereIn('id_dapur', $approvedDapurIds);
        })->with([
            'transaksiDapur.createdBy',
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'transaksiDapur.dapur',
            'stockSnapshots.templateItem'
        ])->findOrFail($approvalId);

        $dapur = $approval->transaksiDapur->dapur;
        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();
        
        // Don't ensure snapshots for Mitra, just view them if they exist
        $stockCheck = $this->enhanceStockCheckWithSnapshots($approval, $dapur, $stockCheck);
        $menuDetails = $this->getMenuDetails($approval->transaksiDapur);

        return view('mitra.laporan-transaksi.show', compact(
            'approval',
            'dapur',
            'stockCheck',
            'menuDetails'
        ));
    }

    private function enhanceStockCheckWithSnapshots(ApprovalTransaksi $approval, $dapur, array $stockCheck): array
    {
        $snapshots = $approval->stockSnapshots->keyBy('id_template_item');

        $hasSnapshots = $snapshots->count() > 0;
        $stockCheck['has_snapshots'] = $hasSnapshots;
        $stockCheck['snapshot_created_at'] = $hasSnapshots ? $approval->created_at : null;

        if ($hasSnapshots) {
            // Fetch current physical stock amounts if needed, though mostly for Kepala Dapur approval.
            // For historical view, rely heavily on snapshot 'available'
            $currentStocks = StockItem::where('id_dapur', $dapur->id_dapur)
                ->whereIn('id_template_item', $snapshots->keys())
                ->pluck('jumlah', 'id_template_item');

            foreach ($stockCheck['ingredients_summary'] as &$ingredient) {
                $snapshot = $snapshots->get($ingredient['id_template_item']);
                if ($snapshot) {
                    $ingredient['current_available'] = $currentStocks->get($ingredient['id_template_item']) ?? 0;
                    $ingredient['available'] = (float)$snapshot->available;
                    $ingredient['sufficient'] = $ingredient['available'] >= $ingredient['needed'];
                    $ingredient['from_snapshot'] = true;
                } else {
                    $ingredient['from_snapshot'] = false;
                }
            }

            $stockCheck['can_produce'] = collect($stockCheck['ingredients_summary'])->every(function ($ingredient) {
                return $ingredient['sufficient'];
            });
        }

        return $stockCheck;
    }

    private function getMenuDetails(TransaksiDapur $transaksi): array
    {
        $menuDetails = [];

        foreach ($transaksi->detailTransaksiDapur as $detail) {
            $requiredIngredients = $detail->menuMakanan->calculateRequiredIngredients($detail->jumlah_porsi);

            $menuDetails[] = [
                'menu' => $detail->menuMakanan,
                'detail' => $detail,
                'ingredients' => $requiredIngredients,
                'total_ingredients' => count($requiredIngredients),
                'formatted_portions' => $detail->jumlah_porsi . ' ' . $detail->getTipePorsiText()
            ];
        }

        return $menuDetails;
    }
}
