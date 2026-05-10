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
            $status = $request->status;
            if ($status === 'with_ulasan') {
                $query->whereHas('transaksiDapur.orderProduksi', function ($q) {
                    $q->whereNotNull('ulasan')->orWhereHas('distribusiOrder', function ($dq) {
                        $dq->whereNotNull('ulasan');
                    });
                });
            } elseif ($status === 'with_kritik') {
                $query->whereHas('transaksiDapur.orderProduksi.distribusiOrder.details', function ($q) {
                    $q->whereNotNull('kritik');
                });
            } elseif ($status === 'with_sisa_dist') {
                $query->whereHas('transaksiDapur.orderProduksi.distribusiOrder', function ($q) {
                    $q->where('status', \App\Models\OrderDistribusi::STATUS_SUDAH_DIKIRIM)
                        ->where(function ($sq) {
                            $sq->whereRaw('(SELECT SUM(porsi_besar) FROM order_distribusi_details WHERE order_distribusi_details.id_distribusi = order_distribusi.id_distribusi AND status = "sudah_dikirim") < (SELECT MAX(jumlah_porsi) FROM detail_transaksi_dapur WHERE detail_transaksi_dapur.id_transaksi = order_distribusi.id_order AND tipe_porsi = "besar")')
                                ->orWhereRaw('(SELECT SUM(porsi_kecil) FROM order_distribusi_details WHERE order_distribusi_details.id_distribusi = order_distribusi.id_distribusi AND status = "sudah_dikirim") < (SELECT MAX(jumlah_porsi) FROM detail_transaksi_dapur WHERE detail_transaksi_dapur.id_transaksi = order_distribusi.id_order AND tipe_porsi = "kecil")');
                        });
                });
            } elseif ($status === 'with_sisa_recv') {
                $query->whereHas('transaksiDapur.orderProduksi.distribusiOrder', function ($q) {
                    $q->where('status', \App\Models\OrderDistribusi::STATUS_SUDAH_DIKIRIM)
                        ->whereHas('details', function ($dq) {
                            $dq->where('status_penerimaan', '!=', \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_MENUNGGU)
                                ->where(function ($sq) {
                                    $sq->whereRaw('porsi_besar > porsi_besar_diterima')
                                        ->orWhereRaw('porsi_kecil > porsi_kecil_diterima');
                                });
                        });
                });
            } else {
                $query->where('status', $status);
            }
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
        
        $stockCheck = $this->enhanceStockCheckWithSnapshots($approval, $dapur, $stockCheck);
        $menuDetails = $this->getMenuDetails($approval->transaksiDapur);

        $summarySisa = [
            'planned_besar' => $approval->transaksiDapur->getTotalPorsiBesar(),
            'planned_kecil' => $approval->transaksiDapur->getTotalPorsiKecil(),
            'sent_besar' => 0,
            'sent_kecil' => 0,
            'received_besar' => 0,
            'received_kecil' => 0,
            'remaining_dist_besar' => 0,
            'remaining_dist_kecil' => 0,
            'remaining_recv_besar' => 0,
            'remaining_recv_kecil' => 0,
            'has_data' => false
        ];

        if ($approval->transaksiDapur->orderProduksi && $approval->transaksiDapur->orderProduksi->distribusiOrder) {
            $distOrder = $approval->transaksiDapur->orderProduksi->distribusiOrder;
            $summarySisa['has_data'] = true;
            
            $summarySisa['sent_besar'] = $distOrder->details->where('status', 'sudah_dikirim')->sum('porsi_besar');
            $summarySisa['sent_kecil'] = $distOrder->details->where('status', 'sudah_dikirim')->sum('porsi_kecil');
            
            $confirmedDetails = $distOrder->details->where('status_penerimaan', '!=', \App\Models\OrderDistribusiDetail::STATUS_PENERIMAAN_MENUNGGU);
            
            $summarySisa['received_besar'] = $confirmedDetails->sum('porsi_besar_diterima');
            $summarySisa['received_kecil'] = $confirmedDetails->sum('porsi_kecil_diterima');
            
            $sentToConfirmedBesar = $confirmedDetails->sum('porsi_besar');
            $sentToConfirmedKecil = $confirmedDetails->sum('porsi_kecil');
            
            $summarySisa['remaining_dist_besar'] = max(0, $summarySisa['planned_besar'] - $summarySisa['sent_besar']);
            $summarySisa['remaining_dist_kecil'] = max(0, $summarySisa['planned_kecil'] - $summarySisa['sent_kecil']);
            
            $summarySisa['remaining_recv_besar'] = max(0, $sentToConfirmedBesar - $summarySisa['received_besar']);
            $summarySisa['remaining_recv_kecil'] = max(0, $sentToConfirmedKecil - $summarySisa['remaining_recv_kecil']);
        }

        $headDistributor = null;
        $headProduksi = null;
        if ($approval->transaksiDapur->orderProduksi) {
            $headProduksi = \App\Models\Produksi::where('id_dapur', $dapur->id_dapur)
                ->where('jabatan', 'Penanggung jawab')
                ->first() ?? \App\Models\Produksi::where('id_dapur', $dapur->id_dapur)->first();

            if ($approval->transaksiDapur->orderProduksi->distribusiOrder) {
                $headDistributor = \App\Models\Distributor::where('id_dapur', $dapur->id_dapur)
                    ->where('jabatan', 'Penanggung jawab')
                    ->first() ?? \App\Models\Distributor::where('id_dapur', $dapur->id_dapur)->first();
            }
        }

        return view('mitra.laporan-transaksi.show', compact(
            'approval',
            'dapur',
            'stockCheck',
            'menuDetails',
            'summarySisa',
            'headDistributor',
            'headProduksi'
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
