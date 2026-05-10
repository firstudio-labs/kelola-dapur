<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mitra;
use App\Models\MitraDokumentasi;
use App\Models\MitraDokumentasiFoto;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DokumentasiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->userRole || $user->userRole->role_type !== 'mitra') {
            abort(403, 'Unauthorized access');
        }

        $mitra = Mitra::where('id_user_role', $user->userRole->id_user_role)->first();
        if (!$mitra) {
            abort(403, 'Mitra profile not found');
        }

        $dapurApproved = $mitra->dapurApproved;
        $dapurIds = $dapurApproved->pluck('id_dapur')->toArray();

        $selectedDapurId = $request->input('dapur');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($selectedDapurId) {
            $dapurIds = array_intersect($dapurIds, [$selectedDapurId]);
        }

        $dokumentasis = MitraDokumentasi::where('id_mitra', $mitra->id_mitra)
            ->whereIn('id_dapur', $dapurIds)
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('tanggal_waktu', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('tanggal_waktu', '<=', $dateTo);
            })
            ->with(['dapur', 'fotos'])
            ->orderBy('tanggal_waktu', 'desc')
            ->paginate(15);

        return view('mitra.dokumentasi.index', compact('dokumentasis', 'dapurApproved', 'selectedDapurId', 'dateFrom', 'dateTo'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $mitra = Mitra::where('id_user_role', $user->userRole->id_user_role)->first();
        
        $request->validate([
            'id_dapur' => 'required|exists:dapur,id_dapur',
            'tanggal_waktu' => 'required|date',
            'keterangan' => 'nullable|string',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $dokumentasi = MitraDokumentasi::create([
            'id_mitra' => $mitra->id_mitra,
            'id_dapur' => $request->id_dapur,
            'tanggal_waktu' => $request->tanggal_waktu,
            'keterangan' => $request->keterangan,
        ]);

        $this->handlePhotos($request, $dokumentasi);

        return redirect()->route('mitra.dokumentasi.index')->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $mitra = Mitra::where('id_user_role', $user->userRole->id_user_role)->first();
        
        $dokumentasi = MitraDokumentasi::where('id_dokumentasi', $id)
            ->where('id_mitra', $mitra->id_mitra)
            ->firstOrFail();

        $request->validate([
            'id_dapur' => 'required|exists:dapur,id_dapur',
            'tanggal_waktu' => 'required|date',
            'keterangan' => 'nullable|string',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Process deleted photos first
        if ($request->has('deleted_photo_ids')) {
            $deletedIds = explode(',', $request->deleted_photo_ids[0] ?? '');
            $deletedIds = array_filter(array_map('trim', $deletedIds));
            
            if (!empty($deletedIds)) {
                $fotosToDelete = MitraDokumentasiFoto::whereIn('id_foto', $deletedIds)
                    ->where('id_dokumentasi', $dokumentasi->id_dokumentasi)
                    ->get();
                    
                foreach ($fotosToDelete as $foto) {
                    if (Storage::disk('public')->exists($foto->url)) {
                        Storage::disk('public')->delete($foto->url);
                    }
                    $foto->delete();
                }
            }
        }

        $dokumentasi->update([
            'id_dapur' => $request->id_dapur,
            'tanggal_waktu' => $request->tanggal_waktu,
            'keterangan' => $request->keterangan,
        ]);

        $this->handlePhotos($request, $dokumentasi);

        return redirect()->route('mitra.dokumentasi.index')->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $mitra = Mitra::where('id_user_role', $user->userRole->id_user_role)->first();
        
        $dokumentasi = MitraDokumentasi::where('id_dokumentasi', $id)
            ->where('id_mitra', $mitra->id_mitra)
            ->firstOrFail();

        foreach ($dokumentasi->fotos as $foto) {
            if (Storage::disk('public')->exists($foto->url)) {
                Storage::disk('public')->delete($foto->url);
            }
        }
        
        $dokumentasi->delete();

        return redirect()->route('mitra.dokumentasi.index')->with('success', 'Dokumentasi berhasil dihapus.');
    }

    private function handlePhotos(Request $request, MitraDokumentasi $dokumentasi)
    {
        if ($request->hasFile('fotos')) {
            $currentFotoCount = $dokumentasi->fotos()->count();
            $newPhotos = $request->file('fotos');
            
            $allowedNewCount = max(0, 5 - $currentFotoCount);
            if ($allowedNewCount > 0) {
                $photosToProcess = array_slice($newPhotos, 0, $allowedNewCount);
                $manager = new ImageManager(new Driver());

                foreach ($photosToProcess as $file) {
                    try {
                        $image = $manager->read($file);
                        $encoded = $image->toWebp(80);
                        
                        $filename = 'mitra/dokumentasi/' . $dokumentasi->id_dokumentasi . '_' . uniqid() . '.webp';
                        
                        Storage::disk('public')->put($filename, $encoded->toString());

                        MitraDokumentasiFoto::create([
                            'id_dokumentasi' => $dokumentasi->id_dokumentasi,
                            'url' => $filename
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error processing photo for Mitra Dokumentasi: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
