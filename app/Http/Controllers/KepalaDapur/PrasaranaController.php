<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\KategoriPrasarana;
use App\Models\ItemPrasarana;
use App\Models\DapurPrasarana;
use App\Models\DapurPrasaranaFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PrasaranaController extends Controller
{
    public function index(Dapur $dapur)
    {
        $dapur->load('prasarana.fotos', 'prasarana.item');

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

        $submittedItems = $request->input('prasarana', []);
        
        $existingItems = $dapur->prasarana()->pluck('id_item')->toArray();

        $itemsToDelete = array_diff($existingItems, $submittedItems);
        if (!empty($itemsToDelete)) {
            $prasaranasToDelete = $dapur->prasarana()->whereIn('id_item', $itemsToDelete)->get();
            foreach ($prasaranasToDelete as $dp) {
                foreach ($dp->fotos as $foto) {
                    $path = str_replace('storage/', '', $foto->foto_url);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                $dp->delete();
            }
        }

        $itemsToAdd = array_diff($submittedItems, $existingItems);
        foreach ($itemsToAdd as $itemId) {
            \App\Models\DapurPrasarana::create([
                'id_dapur' => $dapur->id_dapur,
                'id_item' => $itemId,
                'is_available' => true
            ]);
        }

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Kelengkapan Prasarana berhasil diperbarui');
    }

    public function updateDetail(Request $request, Dapur $dapur, DapurPrasarana $dapurPrasarana)
    {
        if ($dapurPrasarana->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'keterangan' => 'nullable|string',
            'fotos' => 'nullable|array|max:5', 
            'fotos.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', 
            'deleted_photo_ids' => 'nullable|array',
            'deleted_photo_ids.*' => 'exists:dapur_prasarana_foto,id_foto',
        ]);

        $dapurPrasarana->update([
            'keterangan' => $request->keterangan,
        ]);

        if ($request->has('deleted_photo_ids')) {
            $photosToDelete = DapurPrasaranaFoto::whereIn('id_foto', $request->deleted_photo_ids)
                ->where('id_dapur_prasarana', $dapurPrasarana->id_dapur_prasarana)
                ->get();
            
            foreach ($photosToDelete as $photo) {
                $path = str_replace('storage/', '', $photo->foto_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $photo->delete();
            }
        }

        if ($request->hasFile('fotos')) {
            $manager = new ImageManager(new Driver());
            $uploadPath = 'prasarana/' . date('Y/m');
            
            if (!Storage::disk('public')->exists($uploadPath)) {
                Storage::disk('public')->makeDirectory($uploadPath);
            }

            foreach ($request->file('fotos') as $foto) {
                try {
                    $filename = Str::random(40) . '.webp';
                    
                    $image = $manager->read($foto->getRealPath());
                    $image->scaleDown(width: 1200, height: 1200);
                    $encoded = $image->toWebp(80);
                    
                    Storage::disk('public')->put($uploadPath . '/' . $filename, (string) $encoded);
                    
                    DapurPrasaranaFoto::create([
                        'id_dapur_prasarana' => $dapurPrasarana->id_dapur_prasarana,
                        'foto_url' => 'storage/' . $uploadPath . '/' . $filename,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error uploading prasarana foto: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Detail prasarana berhasil diperbarui.');
    }

    public function deleteFoto(Dapur $dapur, DapurPrasaranaFoto $foto)
    {
        $dapurPrasarana = $foto->dapurPrasarana;
        if (!$dapurPrasarana || $dapurPrasarana->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized action.');
        }

        $path = str_replace('storage/', '', $foto->foto_url);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $foto->delete();

        return redirect()->route('kepala-dapur.prasarana.index', $dapur)
            ->with('success', 'Foto berhasil dihapus.');
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
