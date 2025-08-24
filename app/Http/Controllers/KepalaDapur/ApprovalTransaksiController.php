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
    public function index(Request $request)
    {
        $dapurId = $request->query('dapur');
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
            'transaksiDapur.detailTransaksiDapur.menuMakanan'
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
            $query->where('status', $request->status);
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

    public function show(Request $request, $approvalId)
    {
        $dapurId = $request->query('dapur');
        $dapur = Dapur::findOrFail($dapurId);
        $approval = ApprovalTransaksi::with([
            'transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'transaksiDapur.createdBy',
            'stockSnapshots.templateItem'
        ])->findOrFail($approvalId);

        $this->createStockSnapshots($approval, $dapur);

        $stockCheck = $this->getEnhancedStockCheck($approval, $dapur);

        $menuDetails = $this->getDetailedMenuInfo($approval);

        return view('kepaladapur.approval-transaksi.show', compact(
            'approval',
            'dapur',
            'stockCheck',
            'menuDetails'
        ));
    }

    public function approve(Request $request, $approvalId)
    {
        $dapurId = $request->query('dapur');
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
            DB::transaction(function () use ($approval, $request) {
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

    public function reject(Request $request, $dapurId, $approvalId)
    {
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
            DB::transaction(function () use ($approval, $request) {
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

    private function createStockSnapshots(ApprovalTransaksi $approval, Dapur $dapur)
    {
        $existingSnapshots = StockSnapshot::where('id_approval_transaksi', $approval->id_approval_transaksi)->count();

        if ($existingSnapshots > 0) {
            return;
        }

        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

        foreach ($stockCheck['ingredients_summary'] as $ingredient) {
            StockSnapshot::create([
                'id_approval_transaksi' => $approval->id_approval_transaksi,
                'id_template_item' => $ingredient['id_template_item'],
                'available' => $ingredient['available'],
                'satuan' => $ingredient['satuan']
            ]);
        }
    }

    private function getEnhancedStockCheck(ApprovalTransaksi $approval, Dapur $dapur): array
    {
        $stockCheck = $approval->transaksiDapur->checkAllStockAvailability();

        $snapshots = StockSnapshot::where('id_approval_transaksi', $approval->id_approval_transaksi)
            ->with('templateItem')
            ->get()
            ->keyBy('id_template_item');

        foreach ($stockCheck['ingredients_summary'] as &$ingredient) {
            $snapshot = $snapshots->get($ingredient['id_template_item']);
            if ($snapshot) {
                $ingredient['snapshot_available'] = (float)$snapshot->available;
                $ingredient['current_available'] = $ingredient['available'];
                $ingredient['available'] = (float)$snapshot->available;
                $ingredient['sufficient'] = $ingredient['available'] >= $ingredient['needed'];
            }
        }

        $stockCheck['can_produce'] = collect($stockCheck['ingredients_summary'])->every(function ($ingredient) {
            return $ingredient['sufficient'];
        });

        return $stockCheck;
    }

    private function getDetailedMenuInfo(ApprovalTransaksi $approval): array
    {
        $menuDetails = [];

        foreach ($approval->transaksiDapur->detailTransaksiDapur as $detail) {
            $menu = $detail->menuMakanan;
            if (!$menu) continue;

            $baseIngredients = [];
            foreach ($menu->bahanMenu as $bahanMenu) {
                $baseIngredients[] = [
                    'nama_bahan' => $bahanMenu->templateItem->nama_bahan,
                    'jumlah_per_porsi' => $bahanMenu->jumlah,
                    'satuan' => $bahanMenu->templateItem->satuan,
                    'total_needed' => $bahanMenu->jumlah * $detail->jumlah_porsi,
                ];
            }

            $menuDetails[] = [
                'detail' => $detail,
                'menu' => $menu,
                'base_ingredients' => $baseIngredients,
                'jumlah_porsi' => $detail->jumlah_porsi,
                'tipe_porsi' => $detail->tipe_porsi,
            ];
        }

        return $menuDetails;
    }
}
