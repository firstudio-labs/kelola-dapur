<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\KategoriPrasarana;
use App\Models\ItemPrasarana;

class DapurController extends Controller
{
    public function dapurIndex(Request $request)
    {
        $query = Dapur::query();

        if ($search = $request->input('search')) {
            $query->where('nama_dapur', 'like', "%{$search}%")
                ->orWhere('province_name', 'like', "%{$search}%")
                ->orWhere('regency_name', 'like', "%{$search}%")
                ->orWhere('district_name', 'like', "%{$search}%")
                ->orWhere('village_name', 'like', "%{$search}%");
        }

        if ($province = $request->input('filter_provinsi')) {
            $query->where('province_code', $province);
        }

        if ($regency = $request->input('filter_kabupaten')) {
            $query->where('regency_code', $regency);
        }

        if ($district = $request->input('filter_kecamatan')) {
            $query->where('district_code', $district);
        }

        if ($village = $request->input('filter_kelurahan')) {
            $query->where('village_code', $village);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $dapurList = $query->withCount(['kepalaDapur', 'adminGudang', 'ahliGizi'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('superadmin.dapur.index', compact('dapurList'));
    }

    public function dapurCreate()
    {
        $kategoriPrasarana = KategoriPrasarana::with('items')->where('is_active', true)->get();
        return view('superadmin.dapur.create', compact('kategoriPrasarana'));
    }

    public function dapurStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur',
            'no_registrasi_sppg' => 'nullable|string|max:100',
            'nik_pemilik' => 'nullable|string|max:16',
            'tag_lokasi' => 'nullable|string',
            'foto_bangunan' => 'nullable|image|max:2048', 
            'provinsi' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:14',
            'status' => 'required|in:active,inactive',
            'prasarana' => 'nullable|array',
            'prasarana.*' => 'exists:item_prasarana,id_item'
        ], [
            'nama_dapur.required' => 'Nama dapur harus diisi',
            'nama_dapur.unique' => 'Nama dapur sudah digunakan',
            'provinsi.required' => 'Provinsi harus dipilih',
            'kabupaten_kota.required' => 'Kabupaten/Kota harus dipilih',
            'alamat.required' => 'Alamat dapur harus diisi',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
            'foto_bangunan.image' => 'File harus berupa gambar',
            'foto_bangunan.max' => 'Ukuran gambar maksimal 2MB',
            'prasarana.*.exists' => 'Item prasarana tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $wilayah = $request->kabupaten_kota . ', ' . $request->provinsi;

        $fotoBangunanPath = null;
        if ($request->hasFile('foto_bangunan')) {
            $file = $request->file('foto_bangunan');
            $filename = time() . '_' . Str::random(10) . '.webp';
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            if ($image->width() > 1920) {
                $image->scaleDown(width: 1920);
            }
            
            Storage::put('public/dapur/' . $filename, (string) $image->toWebp(80));
            $fotoBangunanPath = 'dapur/' . $filename;
        }

        $dapur = Dapur::create([
            'nama_dapur' => $request->nama_dapur,
            'wilayah' => $wilayah,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'status' => $request->status,
            'no_registrasi_sppg' => $request->no_registrasi_sppg,
            'nik_pemilik' => $request->nik_pemilik,
            'foto_bangunan' => $fotoBangunanPath,
            'tag_lokasi' => $request->tag_lokasi,
        ]);

        if ($request->has('prasarana') && is_array($request->prasarana)) {
            foreach ($request->prasarana as $itemId) {
                \App\Models\DapurPrasarana::create([
                    'id_dapur' => $dapur->id_dapur,
                    'id_item' => $itemId,
                    'is_available' => true
                ]);
            }
        }

        return redirect()->route('superadmin.dapur.index')
            ->with('success', 'Dapur berhasil ditambahkan');
    }

    public function dapurShow(Dapur $dapur)
    {
        $dapur->load(['kepalaDapur.user', 'adminGudang.user', 'ahliGizi.user']);

        $stats = [
            'total_staff' => $dapur->kepalaDapur->count() + $dapur->adminGudang->count() + $dapur->ahliGizi->count(),
            'kepala_dapur_count' => $dapur->kepalaDapur->count(),
            'admin_gudang_count' => $dapur->adminGudang->count(),
            'ahli_gizi_count' => $dapur->ahliGizi->count(),
        ];

        return view('superadmin.dapur.show', compact('dapur', 'stats'));
    }

    public function dapurEdit(Dapur $dapur)
    {
        $dapur->load('prasarana');
        $kategoriPrasarana = KategoriPrasarana::with('items')->where('is_active', true)->get();
        return view('superadmin.dapur.edit', compact('dapur', 'kategoriPrasarana'));
    }

    public function dapurUpdate(Request $request, Dapur $dapur)
    {
        $validator = Validator::make($request->all(), [
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur,' . $dapur->id_dapur . ',id_dapur',
            'no_registrasi_sppg' => 'nullable|string|max:100',
            'nik_pemilik' => 'nullable|string|max:16',
            'tag_lokasi' => 'nullable|string',
            'foto_bangunan' => 'nullable|image|max:2048', 
            'provinsi' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'status' => 'required|in:active,inactive',
            'prasarana' => 'nullable|array',
            'prasarana.*' => 'exists:item_prasarana,id_item'
        ], [
            'nama_dapur.required' => 'Nama dapur harus diisi',
            'nama_dapur.unique' => 'Nama dapur sudah digunakan',
            'provinsi.required' => 'Provinsi harus dipilih',
            'kabupaten_kota.required' => 'Kabupaten/Kota harus dipilih',
            'kecamatan.required' => 'Kecamatan harus dipilih',
            'kelurahan.required' => 'Kelurahan harus dipilih',
            'alamat.required' => 'Alamat dapur harus diisi',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'telepon.regex' => 'Format nomor telepon tidak valid',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
            'foto_bangunan.image' => 'File harus berupa gambar',
            'foto_bangunan.max' => 'Ukuran gambar maksimal 2MB',
            'prasarana.*.exists' => 'Item prasarana tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            
            $wilayahData = $this->extractWilayahDataForUpdate($request, $dapur);

            $fotoBangunanPath = $dapur->foto_bangunan;
            if ($request->hasFile('foto_bangunan')) {
                if ($dapur->foto_bangunan && Storage::exists('public/' . $dapur->foto_bangunan)) {
                    Storage::delete('public/' . $dapur->foto_bangunan);
                }

                $file = $request->file('foto_bangunan');
                $filename = time() . '_' . Str::random(10) . '.webp';
                
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                
                if ($image->width() > 1920) {
                    $image->scaleDown(width: 1920);
                }
                
                Storage::put('public/dapur/' . $filename, (string) $image->toWebp(80));
                $fotoBangunanPath = 'dapur/' . $filename;
            }

            
            $dapur->update([
                'nama_dapur' => trim($request->nama_dapur),
                'province_code' => $wilayahData['province_code'],
                'province_name' => trim($request->provinsi),
                'regency_code' => $wilayahData['regency_code'],
                'regency_name' => trim($request->kabupaten_kota),
                'district_code' => $wilayahData['district_code'],
                'district_name' => trim($request->kecamatan),
                'village_code' => $wilayahData['village_code'],
                'village_name' => trim($request->kelurahan),
                'alamat' => trim($request->alamat),
                'telepon' => $request->telepon ? trim($request->telepon) : null,
                'status' => $request->status,
                'no_registrasi_sppg' => $request->no_registrasi_sppg,
                'nik_pemilik' => $request->nik_pemilik,
                'foto_bangunan' => $fotoBangunanPath,
                'tag_lokasi' => $request->tag_lokasi,
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

            return redirect()->route('superadmin.dapur.index')
                ->with('success', 'Dapur berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating dapur', [
                'dapur_id' => $dapur->id_dapur,
                'error' => $e->getMessage(),
                'input' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui dapur. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function dapurDestroy(Dapur $dapur)
    {
        $hasStaff = $dapur->kepalaDapur()->exists() ||
            $dapur->adminGudang()->exists() ||
            $dapur->ahliGizi()->exists();

        if ($hasStaff) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus dapur yang masih memiliki staff');
        }

        $dapur->delete();

        return redirect()->route('superadmin.dapur.index')
            ->with('success', 'Dapur berhasil dihapus');
    }

    private function extractWilayahData(Request $request): array
    {
        
        if ($request->filled(['province_code', 'regency_code', 'district_code', 'village_code'])) {
            return [
                'province_code' => $request->province_code,
                'regency_code' => $request->regency_code,
                'district_code' => $request->district_code,
                'village_code' => $request->village_code,
            ];
        }

        return $this->findWilayahCodesByName(
            $request->provinsi,
            $request->kabupaten_kota,
            $request->kecamatan,
            $request->kelurahan
        );
    }

    private function extractWilayahDataForUpdate(Request $request, Dapur $dapur): array
    {
        
        if ($request->filled(['province_code', 'regency_code', 'district_code', 'village_code'])) {
            return [
                'province_code' => $request->province_code,
                'regency_code' => $request->regency_code,
                'district_code' => $request->district_code,
                'village_code' => $request->village_code,
            ];
        }

        if (
            $request->provinsi === $dapur->province_name &&
            $request->kabupaten_kota === $dapur->regency_name &&
            $request->kecamatan === $dapur->district_name &&
            $request->kelurahan === $dapur->village_name
        ) {
            return [
                'province_code' => $dapur->province_code,
                'regency_code' => $dapur->regency_code,
                'district_code' => $dapur->district_code,
                'village_code' => $dapur->village_code,
            ];
        }

        return $this->findWilayahCodesByName(
            $request->provinsi,
            $request->kabupaten_kota,
            $request->kecamatan,
            $request->kelurahan
        );
    }

    private function findWilayahCodesByName(string $provinceName, string $regencyName, string $districtName, string $villageName): array
    {

        return [
            'province_code' => '00',
            'regency_code' => '0000',
            'district_code' => '0000000',
            'village_code' => '0000000000',
        ];
    }
}
