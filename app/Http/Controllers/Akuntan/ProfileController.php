<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\Akuntan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $akuntan = $user->akuntan;
        $dapur = $user->userRole->dapur;
        
        return view('akuntan.profile.edit', compact('user', 'akuntan', 'dapur'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $akuntan = $user->akuntan;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'nik_akuntan' => 'nullable|string|max:16',
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
            'nik_akuntan'   => $validated['nik_akuntan']   ?? null,
            'nama_lengkap'  => $validated['nama_lengkap']  ?? null,
            'kontak_wa'     => $validated['kontak_wa']     ?? null,
            'pendidikan'    => $validated['pendidikan']    ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'jabatan'       => $validated['jabatan']       ?? null,
            'alamat_detail' => $validated['alamat_detail'] ?? null,
            'province_code' => $validated['province_code'] ?? null,
            'province_name' => $validated['province_name'] ?? null,
            'regency_code'  => $validated['regency_code']  ?? null,
            'regency_name'  => $validated['regency_name']  ?? null,
            'district_code' => $validated['district_code'] ?? null,
            'district_name' => $validated['district_name'] ?? null,
            'village_code'  => $validated['village_code']  ?? null,
            'village_name'  => $validated['village_name']  ?? null,
        ];

        if ($request->hasFile('foto_diri')) {
            if ($akuntan->foto_diri && Storage::disk('public')->exists($akuntan->foto_diri)) {
                Storage::disk('public')->delete($akuntan->foto_diri);
            }

            $image = $request->file('foto_diri');
            $filename = time() . '_' . uniqid() . '.webp';
            $path = 'dokumen_akuntan/foto_diri/' . $filename;
            
            $manager = new ImageManager(new Driver());
            $img = $manager->read($image->getRealPath());
            if ($img->width() > 1200) {
                $img->scale(width: 1200);
            }
            Storage::disk('public')->put($path, (string) $img->toWebp(80));
            $profileData['foto_diri'] = $path;
        }

        $akuntan->update($profileData);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function editSecurity()
    {
        $user = Auth::user();
        $dapur = $user->userRole->dapur;
        return view('akuntan.profile.security', compact('user', 'dapur'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

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
