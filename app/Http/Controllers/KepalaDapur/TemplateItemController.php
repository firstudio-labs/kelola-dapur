<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:kepala_dapur']);
    }

    public function index()
    {
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->paginate(15);

        return view('kepaladapur.template_item.index', compact('templateItems'));
    }

    public function create()
    {
        $satuans = ['kg', 'liter', 'pcs'];
        // $satuans = ['kg', 'gram', 'liter', 'ml', 'pcs', 'pack', 'botol', 'kaleng', 'ikat', 'buah'];
        return view('kepaladapur.template_item.create', compact('satuans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:100|unique:template_items,nama_bahan',
            'satuan' => 'required|in:kg,gram,liter,ml,pcs,pack,botol,kaleng,ikat,buah',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'nama_bahan.unique' => 'Nama bahan sudah ada',
            'satuan.required' => 'Satuan harus diisi',
            'satuan.in' => 'Satuan tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        TemplateItem::create([
            'nama_bahan' => $request->nama_bahan,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('kepala-dapur.template-items.index')
            ->with('success', 'Template bahan berhasil ditambahkan');
    }

    public function show(TemplateItem $templateItem)
    {
        $templateItem->load(['stockItems.dapur', 'bahanMenu.menuMakanan']);

        return view('kepaladapur.template_item.show', compact('templateItem'));
    }

    public function edit(TemplateItem $templateItem)
    {
        $satuans = ['kg', 'gram', 'liter', 'ml', 'pcs', 'pack', 'botol', 'kaleng', 'ikat', 'buah'];
        return view('kepaladapur.template_item.edit', compact('templateItem', 'satuans'));
    }

    public function update(Request $request, TemplateItem $templateItem)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:100|unique:template_items,nama_bahan,' . $templateItem->id_template_item . ',id_template_item',
            'satuan' => 'required|in:kg,gram,liter,ml,pcs,pack,botol,kaleng,ikat,buah',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'nama_bahan.unique' => 'Nama bahan sudah ada',
            'satuan.required' => 'Satuan harus diisi',
            'satuan.in' => 'Satuan tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $templateItem->update([
            'nama_bahan' => $request->nama_bahan,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('kepala-dapur.template-items.index')
            ->with('success', 'Template bahan berhasil diperbarui');
    }

    public function destroy(TemplateItem $templateItem)
    {
        if ($templateItem->bahanMenu()->exists()) {
            return redirect()->back()
                ->with('error', 'Template bahan tidak dapat dihapus karena sedang digunakan dalam menu');
        }

        if ($templateItem->stockItems()->exists()) {
            return redirect()->back()
                ->with('error', 'Template bahan tidak dapat dihapus karena masih memiliki data stok');
        }

        $templateItem->delete();

        return redirect()->route('kepala-dapur.template-items.index')
            ->with('success', 'Template bahan berhasil dihapus');
    }

    public function getTemplateItems(Request $request)
    {
        $search = $request->get('search');

        $templateItems = TemplateItem::when($search, function ($query, $search) {
            return $query->where('nama_bahan', 'like', "%{$search}%");
        })
            ->select('id_template_item', 'nama_bahan', 'satuan')
            ->orderBy('nama_bahan', 'asc')
            ->limit(20)
            ->get();

        return response()->json($templateItems);
    }
}
