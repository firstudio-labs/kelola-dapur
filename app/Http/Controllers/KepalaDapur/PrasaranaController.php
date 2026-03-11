<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\KategoriPrasarana;
use App\Models\ItemPrasarana;
use Illuminate\Http\Request;

class PrasaranaController extends Controller
{
    public function index(Dapur $dapur)
    {
        $dapur->load('prasarana');

        $kategoriPrasarana = KategoriPrasarana::with('items')->where('is_active', true)->get();

        return view('kepaladapur.prasarana.index', compact('dapur', 'kategoriPrasarana'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        $request->validate([
            'prasarana' => 'nullable|array',
            'prasarana.*' => 'exists:item_prasarana,id_item',
        ], [
            'prasarana.*.exists' => 'Item prasarana tidak valid',
        ]);

        $dapur->prasarana()->delete();

        if ($request->has('prasarana') && is_array($request->prasarana)) {
            foreach ($request->prasarana as $itemId) {
                \App\Models\DapurPrasarana::create([
                    'id_dapur' => $dapur->id_dapur,
                    'id_item' => $itemId,
                    'is_available' => true
                ]);
            }
        }

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Kelengkapan Prasarana berhasil diperbarui');
    }

    public function storeKategori(Request $request, Dapur $dapur)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_prasarana,nama_kategori',
        ]);

        KategoriPrasarana::create([
            'nama_kategori' => $request->nama_kategori,
            'is_active' => true,
        ]);

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Kelompok Prasarana berhasil ditambahkan');
    }

    public function storeItem(Request $request, Dapur $dapur)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori_prasarana,id_kategori',
            'nama_item' => 'required|string|max:150',
        ]);

        ItemPrasarana::create([
            'id_kategori' => $request->id_kategori,
            'nama_item' => $request->nama_item,
            'is_active' => true,
        ]);

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Item Prasarana berhasil ditambahkan');
    }

    public function destroyKategori(Dapur $dapur, $idKategori)
    {
        $kategori = KategoriPrasarana::findOrFail($idKategori);
        
        if ($kategori->is_default) {
            return redirect()->route('kepala-dapur.prasarana.index', $dapur)
                ->with('error', 'Kelompok Prasarana bawaan sistem tidak dapat dihapus');
        }

        $kategori->delete();

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Kelompok Prasarana berhasil dihapus');
    }

    public function destroyItem(Dapur $dapur, $idItem)
    {
        $item = ItemPrasarana::findOrFail($idItem);

        if ($item->is_default) {
            return redirect()->route('kepala-dapur.prasarana.index', $dapur)
                ->with('error', 'Item Prasarana bawaan sistem tidak dapat dihapus');
        }

        $item->delete();

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Item Prasarana berhasil dihapus');
    }
}
