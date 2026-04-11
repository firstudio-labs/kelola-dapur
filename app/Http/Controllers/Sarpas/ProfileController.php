<?php

namespace App\Http\Controllers\Sarpas;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit(Dapur $dapur)
    {
        $user = Auth::user();

        if (!$user->isSarpas($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini untuk dapur ini');
        }

        $sarpas = $user->sarpas()->where('sarpas.id_dapur', $dapur->id_dapur)->first();
        
        return view('sarpas.profile.edit', compact('dapur', 'user', 'sarpas'));
    }

    public function update(Request $request, Dapur $dapur)
    {
        $user = Auth::user();

        if (!$user->isSarpas($dapur->id_dapur)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini untuk dapur ini');
        }

        $sarpas = $user->sarpas()->where('sarpas.id_dapur', $dapur->id_dapur)->first();

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id_user . ',id_user',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'nama_lengkap' => 'nullable|string|max:255',
            'nik_sarpas' => 'nullable|string|max:16',
            'jabatan' => 'nullable|in:Penanggung jawab,Anggota',
            'kontak_wa' => 'nullable|string|max:20',
            'pendidikan' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,Sarjana',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'provinsi' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'province_code' => 'nullable|string|max:10',
            'regency_code' => 'nullable|string|max:10',
            'district_code' => 'nullable|string|max:10',
            'village_code' => 'nullable|string|max:10',
            'alamat_detail' => 'nullable|string',
            'foto_diri' => 'nullable|image|max:2048', 
        ], [
            'nama.required' => 'Nama akun wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'foto_diri.image' => 'File harus berupa gambar',
            'foto_diri.max' => 'Ukuran gambar maksimal 2MB',
            'pendidikan.in' => 'Pilihan pendidikan tidak valid',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid',
            'jabatan.in' => 'Pilihan jabatan tidak valid',
        ]);

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
        ]);

        $province_code = $request->filled('province_code') ? $request->province_code : $sarpas->province_code;
        $regency_code = $request->filled('regency_code') ? $request->regency_code : $sarpas->regency_code;
        $district_code = $request->filled('district_code') ? $request->district_code : $sarpas->district_code;
        $village_code = $request->filled('village_code') ? $request->village_code : $sarpas->village_code;

        $fotoDiriPath = $sarpas->foto_diri;
        if ($request->hasFile('foto_diri')) {
            if ($sarpas->foto_diri && Storage::exists('public/' . $sarpas->foto_diri)) {
                Storage::delete('public/' . $sarpas->foto_diri);
            }

            $file = $request->file('foto_diri');
            $filename = time() . '_' . Str::random(10) . '.webp';
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            if ($image->width() > 1200) {
                $image->scale(width: 1200);
            }
            Storage::put('public/profiles/' . $filename, (string) $image->toWebp(80));
            $fotoDiriPath = 'profiles/' . $filename;
        }

        $sarpas->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nik_sarpas' => $request->nik_sarpas,
            'jabatan' => $request->jabatan,
            'kontak_wa' => $request->kontak_wa,
            'pendidikan' => $request->pendidikan,
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

        return redirect()->route('sarpas.profile.edit', $dapur)
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function editSecurity(Dapur $dapur)
    {
        $user = Auth::user();
        if (!$user->isSarpas($dapur->id_dapur)) {
            abort(403);
        }
        return view('sarpas.profile.security', compact('dapur', 'user'));
    }

    public function updatePassword(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
        if (!$user->isSarpas($dapur->id_dapur)) {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi',
            'new_password.required' => 'Kata sandi baru wajib diisi',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok',
            'new_password.different' => 'Kata sandi baru harus berbeda dengan kata sandi saat ini',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Kata sandi saat ini tidak cocok.');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
