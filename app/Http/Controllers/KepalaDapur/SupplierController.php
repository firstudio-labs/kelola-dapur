<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $dapur = Auth::user()->userRole->dapur;
        $suppliers = Supplier::where('id_dapur', $dapur->id_dapur)->paginate(10);
        return view('kepaladapur.supplier.index', compact('suppliers', 'dapur'));
    }

    public function create()
    {
        $dapur = Auth::user()->userRole->dapur;
        return view('kepaladapur.supplier.create', compact('dapur'));
    }

    public function store(Request $request)
    {
        $dapur = Auth::user()->userRole->dapur;
        
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        Supplier::create([
            'id_dapur' => $dapur->id_dapur,
            'nama_supplier' => $request->nama_supplier,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('kepala-dapur.supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $dapur = Auth::user()->userRole->dapur;
        
        if ($supplier->id_dapur !== $dapur->id_dapur) {
            abort(403);
        }

        $riwayatStok = $supplier->approvalStockItems()
            ->with(['stockItem.templateItem', 'adminGudang.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('kepaladapur.supplier.show', compact('supplier', 'dapur', 'riwayatStok'));
    }

    public function edit(Supplier $supplier)
    {
        $dapur = Auth::user()->userRole->dapur;
        
        if ($supplier->id_dapur !== $dapur->id_dapur) {
            abort(403);
        }

        return view('kepaladapur.supplier.edit', compact('supplier', 'dapur'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $dapur = Auth::user()->userRole->dapur;
        
        if ($supplier->id_dapur !== $dapur->id_dapur) {
            abort(403);
        }

        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $supplier->update($request->only(['nama_supplier', 'kontak', 'alamat', 'keterangan']));

        return redirect()->route('kepala-dapur.supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $dapur = Auth::user()->userRole->dapur;
        
        if ($supplier->id_dapur !== $dapur->id_dapur) {
            abort(403);
        }

        $supplier->delete();

        return redirect()->route('kepala-dapur.supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
