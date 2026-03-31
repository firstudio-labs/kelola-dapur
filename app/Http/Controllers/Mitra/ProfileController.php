<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra;
        return view('mitra.profile.index', compact('user', 'mitra'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id_user . ',id_user',
            'nik_pemilik' => 'required|string|max:20',
            'nama_pemilik' => 'required|string|max:255',
            'alamat_detail' => 'required|string|max:500',
            'province_code' => 'nullable|string|max:20',
            'provinsi' => 'nullable|string|max:255',
            'regency_code' => 'nullable|string|max:20',
            'kabupaten_kota' => 'nullable|string|max:255',
            'district_code' => 'nullable|string|max:20',
            'kecamatan' => 'nullable|string|max:255',
            'village_code' => 'nullable|string|max:20',
            'kelurahan' => 'nullable|string|max:255',
        ]);

        try {
            $user->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'username' => $request->username,
            ]);

            if ($mitra) {
                $mitra->update([
                    'nik_pemilik' => $request->nik_pemilik,
                    'nama_pemilik' => $request->nama_pemilik,
                    'alamat_detail' => $request->alamat_detail,
                    'province_code' => $request->province_code,
                    'province_name' => $request->provinsi,
                    'regency_code' => $request->regency_code,
                    'regency_name' => $request->kabupaten_kota,
                    'district_code' => $request->district_code,
                    'district_name' => $request->kecamatan,
                    'village_code' => $request->village_code,
                    'village_name' => $request->kelurahan,
                ]);
            }

            return back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update mitra profile failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal memperbarui profil.');
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'new_password.regex' => 'Password baru harus mengandung minimal 1 huruf kecil, 1 huruf besar, dan 1 angka.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'current_password.current_password' => 'Password saat ini salah.',
        ]);

        try {
            $user = Auth::user();
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return back()->with('success', 'Password berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update mitra password failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui password.');
        }
    }
}
