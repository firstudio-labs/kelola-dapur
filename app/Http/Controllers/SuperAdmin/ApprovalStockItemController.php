<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalStockItem;
use App\Models\AdminGudang;
use App\Models\KepalaDapur;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalStockItemController extends Controller
{
    public function index()
    {
        $approvalStockItems = ApprovalStockItem::with(['adminGudang', 'kepalaDapur', 'stockItem.templateItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('approval-stock-items.index', compact('approvalStockItems'));
    }

    public function create()
    {
        $adminGudangList = AdminGudang::orderBy('nama', 'asc')->get();
        $kepalaDapurList = KepalaDapur::orderBy('nama', 'asc')->get();
        $stockItems = StockItem::with('templateItem')->orderBy('id_stock_item', 'desc')->get();

        return view('approval-stock-items.create', compact('adminGudangList', 'kepalaDapurList', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_admin_gudang' => 'required|exists:admin_gudang,id_admin_gudang',
            'id_kepala_dapur' => 'required|exists:kepala_dapur,id_kepala_dapur',
            'id_stock_item' => 'required|exists:stock_items,id_stock_item',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'status' => 'required|in:pending,approved,rejected',
            'keterangan' => 'nullable|string|max:500',
            'approved_at' => 'nullable|date'
        ], [
            'id_admin_gudang.required' => 'Admin gudang harus dipilih',
            'id_admin_gudang.exists' => 'Admin gudang tidak valid',
            'id_kepala_dapur.required' => 'Kepala dapur harus dipilih',
            'id_kepala_dapur.exists' => 'Kepala dapur tidak valid',
            'id_stock_item.required' => 'Stock bahan harus dipilih',
            'id_stock_item.exists' => 'Stock bahan tidak valid',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh negatif',
            'satuan.required' => 'Satuan harus diisi',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
            'approved_at.date' => 'Tanggal persetujuan tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ApprovalStockItem::create([
            'id_admin_gudang' => $request->id_admin_gudang,
            'id_kepala_dapur' => $request->id_kepala_dapur,
            'id_stock_item' => $request->id_stock_item,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'approved_at' => $request->approved_at
        ]);

        return redirect()->route('superadmin.approval-stock-items.index')
            ->with('success', 'Persetujuan stock bahan berhasil ditambahkan');
    }

    public function show(ApprovalStockItem $approvalStockItem)
    {
        $approvalStockItem->load(['adminGudang', 'kepalaDapur', 'stockItem.templateItem']);

        return view('approval-stock-items.show', compact('approvalStockItem'));
    }

    public function edit(ApprovalStockItem $approvalStockItem)
    {
        $adminGudangList = AdminGudang::orderBy('nama', 'asc')->get();
        $kepalaDapurList = KepalaDapur::orderBy('nama', 'asc')->get();
        $stockItems = StockItem::with('templateItem')->orderBy('id_stock_item', 'desc')->get();

        return view('approval-stock-items.edit', compact('approvalStockItem', 'adminGudangList', 'kepalaDapurList', 'stockItems'));
    }

    public function update(Request $request, ApprovalStockItem $approvalStockItem)
    {
        $validator = Validator::make($request->all(), [
            'id_admin_gudang' => 'required|exists:admin_gudang,id_admin_gudang',
            'id_kepala_dapur' => 'required|exists:kepala_dapur,id_kepala_dapur',
            'id_stock_item' => 'required|exists:stock_items,id_stock_item',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'status' => 'required|in:pending,approved,rejected',
            'keterangan' => 'nullable|string|max:500',
            'approved_at' => 'nullable|date'
        ], [
            'id_admin_gudang.required' => 'Admin gudang harus dipilih',
            'id_admin_gudang.exists' => 'Admin gudang tidak valid',
            'id_kepala_dapur.required' => 'Kepala dapur harus dipilih',
            'id_kepala_dapur.exists' => 'Kepala dapur tidak valid',
            'id_stock_item.required' => 'Stock bahan harus dipilih',
            'id_stock_item.exists' => 'Stock bahan tidak valid',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh negatif',
            'satuan.required' => 'Satuan harus diisi',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
            'approved_at.date' => 'Tanggal persetujuan tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $approvalStockItem->update([
            'id_admin_gudang' => $request->id_admin_gudang,
            'id_kepala_dapur' => $request->id_kepala_dapur,
            'id_stock_item' => $request->id_stock_item,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'approved_at' => $request->approved_at
        ]);

        return redirect()->route('superadmin.approval-stock-items.index')
            ->with('success', 'Persetujuan stock bahan berhasil diperbarui');
    }

    public function destroy(ApprovalStockItem $approvalStockItem)
    {
        if ($approvalStockItem->isApproved()) {
            return redirect()->back()
                ->with('error', 'Persetujuan stock bahan yang sudah disetujui tidak dapat dihapus');
        }

        $approvalStockItem->delete();

        return redirect()->route('superadmin.approval-stock-items.index')
            ->with('success', 'Persetujuan stock bahan berhasil dihapus');
    }

    public function approve(ApprovalStockItem $approvalStockItem)
    {
        if (!$approvalStockItem->isPending()) {
            return redirect()->back()
                ->with('error', 'Persetujuan stock bahan hanya dapat disetujui jika berstatus pending');
        }

        if ($approvalStockItem->approve()) {
            return redirect()->route('superadmin.approval-stock-items.index')
                ->with('success', 'Persetujuan stock bahan berhasil disetujui');
        }

        return redirect()->back()
            ->with('error', 'Gagal menyetujui persetujuan stock bahan');
    }

    public function reject(ApprovalStockItem $approvalStockItem)
    {
        if (!$approvalStockItem->isPending()) {
            return redirect()->back()
                ->with('error', 'Persetujuan stock bahan hanya dapat ditolak jika berstatus pending');
        }

        if ($approvalStockItem->reject()) {
            return redirect()->route('superadmin.approval-stock-items.index')
                ->with('success', 'Persetujuan stock bahan berhasil ditolak');
        }

        return redirect()->back()
            ->with('error', 'Gagal menolak persetujuan stock bahan');
    }

    public function getApprovalStockItems(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $approvalStockItems = ApprovalStockItem::with(['stockItem.templateItem', 'adminGudang', 'kepalaDapur'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('stockItem.templateItem', function ($q) use ($search) {
                    $q->where('nama_bahan', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->select('id_approval_stock_item', 'id_admin_gudang', 'id_kepala_dapur', 'id_stock_item', 'jumlah', 'satuan', 'status')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($approvalStockItems);
    }
}
