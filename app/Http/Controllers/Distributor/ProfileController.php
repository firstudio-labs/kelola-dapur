<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $distributor = $user->distributor;
        
        return view('distributor.profile.edit', compact('user', 'distributor'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $distributor = $user->distributor;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'nik_distribusi' => 'nullable|string|max:16',
            'nama_lengkap' => 'nullable|string|max:255',
            'kontak_wa' => 'nullable|string|max:20',
            'pendidikan' => 'nullable|in:SD,SMP,SMA,D1,D2,D3,Sarjana',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'jabatan' => 'nullable|string|max:255',
            'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'alamat_detail' => 'nullable|string',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'regency_code' => 'nullable|string',
            'regency_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
        ]);

        $user->update([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        $profileData = [
            'nik_distribusi' => $validated['nik_distribusi'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'kontak_wa' => $validated['kontak_wa'],
            'pendidikan' => $validated['pendidikan'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'jabatan' => $validated['jabatan'],
            'alamat_detail' => $validated['alamat_detail'],
            'province_code' => $validated['province_code'],
            'province_name' => $validated['province_name'],
            'regency_code' => $validated['regency_code'],
            'regency_name' => $validated['regency_name'],
            'district_code' => $validated['district_code'],
            'district_name' => $validated['district_name'],
            'village_code' => $validated['village_code'],
            'village_name' => $validated['village_name'],
        ];

        if ($request->hasFile('foto_diri')) {
            if ($distributor->foto_diri && Storage::disk('public')->exists($distributor->foto_diri)) {
                Storage::disk('public')->delete($distributor->foto_diri);
            }

            $image = $request->file('foto_diri');
            $filename = time() . '_' . uniqid() . '.webp';
            $path = 'dokumen_distribusi/foto_diri/' . $filename;
            
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image->getRealPath());
            if ($img->width() > 1200) {
                $img->scale(width: 1200);
            }
            Storage::disk('public')->put($path, (string) $img->toWebp(80));
            $profileData['foto_diri'] = $path;
        }

        $distributor->update($profileData);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function editSecurity()
    {
        $user = auth()->user();
        return view('distributor.profile.security', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

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
