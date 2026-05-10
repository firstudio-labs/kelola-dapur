<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\TransaksiDapur;
use App\Models\ApprovalTransaksi;
use App\Models\UserRole;
use App\Models\KepalaDapur;
use App\Models\StockSnapshot;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalTransaksiController extends Controller
{
    
    public function index(Request $request, $dapur)
    {
        
        $dapurId = $dapur;
        $dapur = Dapur::findOrFail($dapurId);

        $user = Auth::user();
        $userRole = UserRole::where('id_user', $user->id_user)
            ->where('role_type', 'kepala_dapur')
            ->where('id_dapur', $dapur->id_dapur)
            ->first();

        if (!$userRole) {
            Log::error('UserRole not found', [
                'user_id' => $user->id_user,
                'dapur_id' => $dapur->id_dapur,
                'role_type' => 'kepala_dapur'
            ]);
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur untuk dapur ini.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $userRole->id_user_role)->first();

        if (!$kepalaDapur) {
            Log::error('KepalaDapur not found', ['id_user_role' => $userRole->id_user_role]);
            return redirect()->back()->with('error', 'Kepala Dapur tidak ditemukan untuk user ini.');
        }

        $query = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
            $q->where('id_dapur', $dapur->id_dapur);
        })->with([
            'transaksiDapur.createdBy',
            'transaksiDapur.detailTransaksiDapur.menuMakanan',
            'transaksiDapur.orderProduksi.distribusiOrder.details'
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('transaksiDapur', function ($q) use ($search) {
                $q->where('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('createdBy', function ($q) use ($search) {
                        $q->where('nama', 'like', '%' . $search . '%');
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

        $approvals = $query->paginate(10);

        $stats = [
            'total' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->count(),
            'pending' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->where('status', 'pending')->count(),
            'approved' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->where('status', 'approved')->count(),
            'rejected' => ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->where('status', 'rejected')->count(),
        ];

        return view('kepaladapur.approval-transaksi.index', compact('approvals', 'dapur', 'stats'));
    }

    public function show(Request $request, $dapur, $approvalId)
    {
        $dapur = Dapur::findOrFail($dapur);

        $approval = ApprovalTransaksi::with([
            'transaksiDapur.createdBy',
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'stockSnapshots.templateItem'
        ])->findOrFail($approvalId);

        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

        if ($approval->isPending()) {
            $this->ensureStockSnapshots($approval, $stockCheck);
        }

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
            
            // Sisa Penerimaan hanya dihitung untuk detail yang sudah dikonfirmasi (diterima/ditolak)
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

        return view('kepaladapur.approval-transaksi.show', compact(
            'approval',
            'dapur',
            'stockCheck',
            'menuDetails',
            'summarySisa',
            'headDistributor',
            'headProduksi'
        ));
    }

    public function approve(Request $request, $dapur, $approvalId)
    {
        
        $dapurId = $dapur;
        $dapur = Dapur::findOrFail($dapurId);
        $approval = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
            $q->where('id_dapur', $dapur->id_dapur);
        })->findOrFail($approvalId);

        $user = Auth::user();
        $userRole = UserRole::where('id_user', $user->id_user)
            ->where('role_type', 'kepala_dapur')
            ->where('id_dapur', $dapur->id_dapur)
            ->first();

        if (!$userRole) {
            Log::error('UserRole not found', [
                'user_id' => $user->id_user,
                'dapur_id' => $dapur->id_dapur,
                'role_type' => 'kepala_dapur'
            ]);
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur untuk dapur ini.');
        }

        $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($approval, $request, $dapur) {
                $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();
                $this->ensureStockSnapshots($approval, $stockCheck);

                $approval->approve($request->catatan_approval);
            });

            return redirect()->route('kepala-dapur.approval-transaksi.index', ['dapur' => $dapur->id_dapur])
                ->with('success', 'Transaksi berhasil disetujui.');
        } catch (\Exception $e) {
            Log::error('Approval error: ' . $e->getMessage(), [
                'approval_id' => $approval->id_approval_transaksi,
                'user_id' => $user->id_user
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $dapur, $approvalId)
    {
        
        $dapurId = $dapur;
        $dapur = Dapur::findOrFail($dapurId);
        $approval = ApprovalTransaksi::whereHas('transaksiDapur', function ($q) use ($dapur) {
            $q->where('id_dapur', $dapur->id_dapur);
        })->findOrFail($approvalId);

        $user = Auth::user();
        $userRole = UserRole::where('id_user', $user->id_user)
            ->where('role_type', 'kepala_dapur')
            ->where('id_dapur', $dapur->id_dapur)
            ->first();

        if (!$userRole) {
            Log::error('UserRole not found', [
                'user_id' => $user->id_user,
                'dapur_id' => $dapur->id_dapur,
                'role_type' => 'kepala_dapur'
            ]);
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur untuk dapur ini.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($approval, $request, $dapur) {
                $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();
                $this->ensureStockSnapshots($approval, $stockCheck);

                $approval->reject($request->alasan_penolakan);
            });

            return redirect()->route('kepala-dapur.approval-transaksi.index', ['dapur' => $dapur->id_dapur])
                ->with('success', 'Transaksi berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Reject error: ' . $e->getMessage(), [
                'approval_id' => $approval->id_approval_transaksi,
                'user_id' => $user->id_user
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request, $dapurId)
    {
        $dapur = Dapur::findOrFail($dapurId);
        $user = Auth::user();
        $userRole = UserRole::where('id_user', $user->id_user)
            ->where('role_type', 'kepala_dapur')
            ->where('id_dapur', $dapur->id_dapur)
            ->first();

        if (!$userRole) {
            Log::error('UserRole not found', [
                'user_id' => $user->id_user,
                'dapur_id' => $dapur->id_dapur,
                'role_type' => 'kepala_dapur'
            ]);
            return redirect()->back()->with('error', 'Anda tidak memiliki akses sebagai Kepala Dapur untuk dapur ini.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $userRole->id_user_role)->first();

        if (!$kepalaDapur) {
            Log::error('KepalaDapur not found', ['id_user_role' => $userRole->id_user_role]);
            return redirect()->back()->with('error', 'Kepala Dapur tidak ditemukan untuk user ini.');
        }

        $request->validate([
            'approval_ids' => 'required|array',
            'approval_ids.*' => 'exists:approval_transaksi,id_approval_transaksi',
            'bulk_action' => 'required|in:approve,reject',
            'bulk_keterangan' => 'nullable|string|max:500',
        ]);

        $approvalIds = $request->approval_ids;
        $action = $request->bulk_action;
        $keterangan = $request->bulk_keterangan;

        if ($action === 'reject' && !$keterangan) {
            return redirect()->back()
                ->withErrors(['bulk_keterangan' => 'Alasan penolakan wajib diisi untuk aksi tolak.']);
        }

        try {
            $processedCount = 0;
            $errorCount = 0;

            DB::transaction(function () use ($approvalIds, $action, $keterangan, $dapur, &$processedCount, &$errorCount) {
                $approvals = ApprovalTransaksi::whereIn('id_approval_transaksi', $approvalIds)
                    ->whereHas('transaksiDapur', function ($q) use ($dapur) {
                        $q->where('id_dapur', $dapur->id_dapur);
                    })
                    ->where('status', 'pending')
                    ->get();

                foreach ($approvals as $approval) {
                    try {
                        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();
                        $this->ensureStockSnapshots($approval, $stockCheck);

                        if ($action === 'approve') {
                            $result = $approval->approve($keterangan);
                            if ($result) {
                                $processedCount++;
                            } else {
                                $errorCount++;
                            }
                        } else {
                            $result = $approval->reject($keterangan);
                            if ($result) {
                                $processedCount++;
                            } else {
                                $errorCount++;
                            }
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error('Bulk approval error: ' . $e->getMessage(), [
                            'approval_id' => $approval->id_approval_transaksi,
                            'user_id' => Auth::user()->id_user
                        ]);
                    }
                }
            });

            $actionText = $action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "{$processedCount} transaksi berhasil {$actionText}.";

            if ($errorCount > 0) {
                $message .= " {$errorCount} transaksi gagal diproses.";
            }

            return redirect()->route('kepala-dapur.approval-transaksi.index', ['dapur' => $dapur->id_dapur])
                ->with($errorCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage(), [
                'user_id' => $user->id_user,
                'dapur_id' => $dapur->id_dapur
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function ensureStockSnapshots(ApprovalTransaksi $approval, array $stockCheck)
    {
        $existingSnapshotsCount = $approval->stockSnapshots()->count();

        if ($existingSnapshotsCount > 0) {
            return;
        }

        foreach ($stockCheck['ingredients_summary'] as $ingredient) {
            StockSnapshot::create([
                'id_approval_transaksi' => $approval->id_approval_transaksi,
                'id_template_item' => $ingredient['id_template_item'],
                'available' => $ingredient['available'],
                'satuan' => $ingredient['satuan']
            ]);
        }

        Log::info('Stock snapshots created', [
            'approval_id' => $approval->id_approval_transaksi,
            'snapshots_count' => count($stockCheck['ingredients_summary'])
        ]);
    }

    private function enhanceStockCheckWithSnapshots(ApprovalTransaksi $approval, Dapur $dapur, array $stockCheck): array
    {
        $snapshots = $approval->stockSnapshots->keyBy('id_template_item');

        $hasSnapshots = $snapshots->count() > 0;
        $stockCheck['has_snapshots'] = $hasSnapshots;
        $stockCheck['snapshot_created_at'] = $hasSnapshots ? $approval->created_at : null;

        if ($hasSnapshots) {
            
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
