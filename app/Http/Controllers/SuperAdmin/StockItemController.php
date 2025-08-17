<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\Dapur;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockItemController extends Controller
{
    public function index()
    {
        $stockItems = StockItem::with(['dapur', 'templateItem'])
            ->orderBy('tanggal_restok', 'desc')
            ->paginate(15);

        return view('stock-items.index', compact('stockItems'));
    }

    public function create()
    {
        $dapurList = Dapur::orderBy('nama_dapur', 'asc')->get();
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('stock-items.create', compact('dapurList', 'templateItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_dapur' => 'required|exists:dapur,id_dapur',
            'id_template_item' => 'required|exists:template_items,id_template_item',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'tanggal_restok' => 'required|date',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'id_dapur.required' => 'Dapur harus dipilih',
            'id_dapur.exists' => 'Dapur tidak valid',
            'id_template_item.required' => 'Bahan harus dipilih',
            'id_template_item.exists' => 'Bahan tidak valid',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh negatif',
            'satuan.required' => 'Satuan harus diisi',
            'tanggal_restok.required' => 'Tanggal restok harus diisi',
            'tanggal_restok.date' => 'Tanggal restok tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        StockItem::create([
            'id_dapur' => $request->id_dapur,
            'id_template_item' => $request->id_template_item,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'tanggal_restok' => $request->tanggal_restok,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('superadmin.stock-items.index')
            ->with('success', 'Stock bahan berhasil ditambahkan');
    }

    public function show(StockItem $stockItem)
    {
        $stockItem->load(['dapur', 'templateItem', 'approvalStockItems']);

        return view('stock-items.show', compact('stockItem'));
    }

    public function edit(StockItem $stockItem)
    {
        $dapurList = Dapur::orderBy('nama_dapur', 'asc')->get();
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('stock-items.edit', compact('stockItem', 'dapurList', 'templateItems'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $validator = Validator::make($request->all(), [
            'id_dapur' => 'required|exists:dapur,id_dapur',
            'id_template_item' => 'required|exists:template_items,id_template_item',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'tanggal_restok' => 'required|date',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'id_dapur.required' => 'Dapur harus dipilih',
            'id_dapur.exists' => 'Dapur tidak valid',
            'id_template_item.required' => 'Bahan harus dipilih',
            'id_template_item.exists' => 'Bahan tidak valid',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah tidak boleh negatif',
            'satuan.required' => 'Satuan harus diisi',
            'tanggal_restok.required' => 'Tanggal restok harus diisi',
            'tanggal_restok.date' => 'Tanggal restok tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $stockItem->update([
            'id_dapur' => $request->id_dapur,
            'id_template_item' => $request->id_template_item,
            'jumlah' => $request->jumlah,
            'satuan' => $request->satuan,
            'tanggal_restok' => $request->tanggal_restok,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('superadmin.stock-items.index')
            ->with('success', 'Stock bahan berhasil diperbarui');
    }

    public function destroy(StockItem $stockItem)
    {
        if ($stockItem->approvalStockItems()->exists()) {
            return redirect()->back()
                ->with('error', 'Stock bahan tidak dapat dihapus karena memiliki data persetujuan terkait');
        }

        $stockItem->delete();

        return redirect()->route('superadmin.stock-items.index')
            ->with('success', 'Stock bahan berhasil dihapus');
    }

    public function getStockItems(Request $request)
    {
        $search = $request->get('search');
        $id_dapur = $request->get('id_dapur');

        $stockItems = StockItem::with(['templateItem', 'dapur'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('templateItem', function ($q) use ($search) {
                    $q->where('nama_bahan', 'like', "%{$search}%");
                });
            })
            ->when($id_dapur, function ($query, $id_dapur) {
                return $query->where('id_dapur', $id_dapur);
            })
            ->select('id_stock_item', 'id_dapur', 'id_template_item', 'jumlah', 'satuan')
            ->orderBy('id_stock_item', 'desc')
            ->limit(20)
            ->get();

        return response()->json($stockItems);
    }
}
