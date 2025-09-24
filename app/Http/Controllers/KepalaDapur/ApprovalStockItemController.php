<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\ApprovalStockItem;
use App\Models\Dapur;
use App\Models\KepalaDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalStockItemController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $dapurId = $request->query('dapur') ?? session('id_dapur');
        $dapur = Dapur::findOrFail($dapurId);
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $user->userRole->id_user_role)->first();

        if (!$kepalaDapur) {
            abort(403, 'Kepala dapur tidak ditemukan untuk user ini.');
        }

        $query = ApprovalStockItem::with(['adminGudang.user', 'stockItem.templateItem', 'stockItem.dapur'])
            ->where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->whereHas('stockItem', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            });

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('stockItem.templateItem', function ($subQ) use ($searchTerm) {
                    $subQ->where('nama_bahan', 'like', "%{$searchTerm}%");
                })
                    ->orWhereHas('adminGudang.user', function ($subQ) use ($searchTerm) {
                        $subQ->where('nama', 'like', "%{$searchTerm}%");
                    })
                    ->orWhere('keterangan', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        if ($sortBy === 'nama_bahan') {
            $query->join('stock_items', 'approval_stock_items.id_stock_item', '=', 'stock_items.id_stock_item')
                ->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
                ->orderBy('template_items.nama_bahan', $sortOrder)
                ->select('approval_stock_items.*');
        } elseif ($sortBy === 'admin_name') {
            $query->join('admin_gudang', 'approval_stock_items.id_admin_gudang', '=', 'admin_gudang.id_admin_gudang')
                ->join('user_roles', 'admin_gudang.id_user_role', '=', 'user_roles.id_user_role')
                ->join('users', 'user_roles.id_user', '=', 'users.id_user')
                ->orderBy('users.nama', $sortOrder)
                ->select('approval_stock_items.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $approvals = $query->paginate(15)->appends($request->query());

        $totalApprovals = ApprovalStockItem::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->whereHas('stockItem', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->count();

        $pendingApprovals = ApprovalStockItem::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->where('status', 'pending')
            ->whereHas('stockItem', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->count();

        $approvedApprovals = ApprovalStockItem::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->where('status', 'approved')
            ->whereHas('stockItem', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->count();

        $rejectedApprovals = ApprovalStockItem::where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
            ->where('status', 'rejected')
            ->whereHas('stockItem', function ($q) use ($dapur) {
                $q->where('id_dapur', $dapur->id_dapur);
            })->count();

        $statusOptions = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];

        return view('kepaladapur.approval_stock_item.index', compact(
            'approvals',
            'dapur',
            'totalApprovals',
            'pendingApprovals',
            'approvedApprovals',
            'rejectedApprovals',
            'statusOptions'
        ));
    }

    public function show(Dapur $dapur, ApprovalStockItem $approval)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $user->userRole->id_user_role)->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Approval tidak ditemukan atau bukan milik Anda.');
        }

        $approval->load([
            'adminGudang.user',
            'stockItem.dapur',
            'stockItem.templateItem'
        ]);

        return view('kepaladapur.approval_stock_item.show', compact('approval', 'dapur'));
    }

    public function approve(Request $request, Dapur $dapur, ApprovalStockItem $approval)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $user->userRole->id_user_role)->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Approval tidak ditemukan atau bukan milik Anda.');
        }

        if (!$approval->isPending()) {
            return redirect()->back()->with('error', 'Approval sudah diproses sebelumnya.');
        }

        $request->validate([
            'keterangan_approval' => 'nullable|string|max:500'
        ], [
            'keterangan_approval.max' => 'Keterangan maksimal 500 karakter'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('keterangan_approval')) {
                $approval->keterangan = $approval->keterangan .
                    ($approval->keterangan ? "\n\nCatatan Kepala Dapur: " : "Catatan Kepala Dapur: ") .
                    $request->keterangan_approval;
                $approval->save();
            }

            if ($approval->approve()) {
                DB::commit();
                return redirect()->route('kepala-dapur.approvals.index', $dapur)
                    ->with('success', 'Permintaan stok berhasil disetujui dan stok telah ditambahkan.');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal menyetujui permintaan stok.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Dapur $dapur, ApprovalStockItem $approval)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $kepalaDapur = KepalaDapur::where('id_user_role', $user->userRole->id_user_role)->first();

        if (!$kepalaDapur || $approval->id_kepala_dapur !== $kepalaDapur->id_kepala_dapur) {
            abort(403, 'Approval tidak ditemukan atau bukan milik Anda.');
        }

        if (!$approval->isPending()) {
            return redirect()->back()->with('error', 'Approval sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500'
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter'
        ]);

        try {
            DB::beginTransaction();

            $approval->keterangan = $approval->keterangan .
                ($approval->keterangan ? "\n\nAlasan Penolakan: " : "Alasan Penolakan: ") .
                $request->alasan_penolakan;
            $approval->save();

            if ($approval->reject()) {
                DB::commit();
                return redirect()->route('kepala-dapur.approvals.index', $dapur)
                    ->with('success', 'Permintaan stok berhasil ditolak.');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal menolak permintaan stok.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'kepala_dapur' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $request->validate([
            'approval_ids' => 'required|array|min:1',
            'approval_ids.*' => 'exists:approval_stock_items,id_approval_stock_item',
            'bulk_action' => 'required|in:approve,reject',
            'bulk_keterangan' => 'nullable|string|max:500'
        ], [
            'approval_ids.required' => 'Pilih minimal 1 permintaan',
            'approval_ids.min' => 'Pilih minimal 1 permintaan',
            'bulk_action.required' => 'Pilih aksi yang akan dilakukan',
            'bulk_keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        $kepalaDapur = KepalaDapur::where('id_user_role', $user->userRole->id_user_role)->first();

        if (!$kepalaDapur) {
            abort(403, 'Kepala dapur tidak ditemukan untuk user ini.');
        }

        try {
            DB::beginTransaction();

            $approvals = ApprovalStockItem::whereIn('id_approval_stock_item', $request->approval_ids)
                ->where('id_kepala_dapur', $kepalaDapur->id_kepala_dapur)
                ->where('status', 'pending')
                ->whereHas('stockItem', function ($q) use ($dapur) {
                    $q->where('id_dapur', $dapur->id_dapur);
                })
                ->get();

            if ($approvals->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada permintaan yang dapat diproses.');
            }

            $processedCount = 0;
            $failedCount = 0;

            foreach ($approvals as $approval) {
                if ($request->filled('bulk_keterangan')) {
                    $approval->keterangan = $approval->keterangan .
                        ($approval->keterangan ? "\n\nCatatan Kepala Dapur: " : "Catatan Kepala Dapur: ") .
                        $request->bulk_keterangan;
                    $approval->save();
                }

                if ($request->bulk_action === 'approve') {
                    if ($approval->approve()) {
                        $processedCount++;
                    } else {
                        $failedCount++;
                    }
                } else {
                    if ($approval->reject()) {
                        $processedCount++;
                    } else {
                        $failedCount++;
                    }
                }
            }

            DB::commit();

            $action = $request->bulk_action === 'approve' ? 'disetujui' : 'ditolak';
            $message = "Berhasil memproses {$processedCount} permintaan ({$action})";

            if ($failedCount > 0) {
                $message .= ". {$failedCount} permintaan gagal diproses.";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
