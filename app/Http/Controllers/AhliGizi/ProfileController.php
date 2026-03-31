<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit(Dapur $dapur)
    {
        
        $user = Auth::user();

        if (!$user->isAhliGizi($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini untuk dapur ini');
        }

        $ahliGizi = $user->ahliGizi()->where('ahli_gizi.id_dapur', $dapur->id_dapur)->first();
        
        return view('ahligizi.profile.edit', compact('dapur', 'user', 'ahliGizi'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        
        $user = Auth::user();

        if (!$user->isAhliGizi($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini untuk dapur ini');
        }

        $ahliGizi = $user->ahliGizi()->where('ahli_gizi.id_dapur', $dapur->id_dapur)->first();

        $request->validate([
            'nama' => 'required|string|max:255',
            'nik_ahli_gizi' => 'nullable|string|max:16',
            'jabatan' => 'nullable|in:Penanggung jawab,Anggota',
            'kontak_wa' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,Sarjana',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'provinsi' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'alamat_detail' => 'nullable|string',
            'foto_diri' => 'nullable|image|max:2048', 
        ], [
            'nama.required' => 'Nama lengkap wajib diisi',
            'foto_diri.image' => 'File harus berupa gambar',
            'foto_diri.max' => 'Ukuran gambar maksimal 2MB',
            'pendidikan_terakhir.in' => 'Pilihan pendidikan tidak valid',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid',
            'jabatan.in' => 'Pilihan jabatan tidak valid',
        ]);

        $user->update([
            'nama' => $request->nama
        ]);

        $province_code = $request->filled('province_code') ? $request->province_code : $ahliGizi->province_code;
        $regency_code = $request->filled('regency_code') ? $request->regency_code : $ahliGizi->regency_code;
        $district_code = $request->filled('district_code') ? $request->district_code : $ahliGizi->district_code;
        $village_code = $request->filled('village_code') ? $request->village_code : $ahliGizi->village_code;

        $fotoDiriPath = $ahliGizi->foto_diri;
        if ($request->hasFile('foto_diri')) {
            
            if ($ahliGizi->foto_diri && Storage::exists('public/' . $ahliGizi->foto_diri)) {
                Storage::delete('public/' . $ahliGizi->foto_diri);
            }

            $file = $request->file('foto_diri');
            $filename = time() . '_' . Str::random(10) . '.webp';
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            if ($image->width() > 1200) {
                $image->scaleDown(width: 1200);
            }
            
            Storage::put('public/profiles/' . $filename, (string) $image->toWebp(80));
            $fotoDiriPath = 'profiles/' . $filename;
        }

        $ahliGizi->update([
            'nik_ahli_gizi' => $request->nik_ahli_gizi,
            'jabatan' => $request->jabatan,
            'kontak_wa' => $request->kontak_wa,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat_detail' => $request->alamat_detail,
            'province_code' => $province_code,
            'province_name' => $request->provinsi,
            'regency_code' => $regency_code,
            'regency_name' => $request->kabupaten_kota,
            'district_code' => $district_code,
            'district_name' => $request->kecamatan,
            'village_code' => $village_code,
            'village_name' => $request->kelurahan,
            'foto_diri' => $fotoDiriPath,
        ]);

        return redirect()->route('ahli-gizi.profile.edit', $dapur)
            ->with('success', 'Profil berhasil diperbarui');
    }
}
