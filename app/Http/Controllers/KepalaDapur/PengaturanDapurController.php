<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PengaturanDapurController extends Controller
{
    public function edit(Dapur $dapur)
    {
        return view('kepaladapur.pengaturan_dapur.edit', compact('dapur'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        $request->validate([
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
            'foto_bangunan.image' => 'File harus berupa gambar',
            'foto_bangunan.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            $wilayahData = $this->extractWilayahDataForUpdate($request, $dapur);
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
                'no_registrasi_sppg' => $request->no_registrasi_sppg,
                'nik_pemilik' => $request->nik_pemilik,
                'foto_bangunan' => $fotoBangunanPath,
                'tag_lokasi' => $request->tag_lokasi,
            ]);

            return redirect()->route('kepala-dapur.pengaturan-dapur.edit', $dapur->id_dapur)
                ->with('success', 'Informasi Dapur berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data dapur: ' . $e->getMessage());
        }
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
