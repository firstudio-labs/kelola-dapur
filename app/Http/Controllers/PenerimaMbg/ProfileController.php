<?php

namespace App\Http\Controllers\PenerimaMbg;

use App\Http\Controllers\Controller;
use App\Models\PenerimaMbg;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)->firstOrFail();
        $dapurList = Dapur::where('status', 'active')->orderBy('nama_dapur')->get();

        return view('penerima_mbg.profile.edit', compact('user', 'penerima', 'dapurList'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)->firstOrFail();

        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'id_dapur'         => 'required|exists:dapur,id_dapur',
            'id_type'          => 'required|in:nik,nisn,no_registrasi',
            'id_number'        => 'required|string|max:50',
            'penanggung_jawab' => 'required|string|max:255',
            'province_code'    => 'required|string',
            'province_name'    => 'required|string|max:100',
            'regency_code'     => 'required|string',
            'regency_name'     => 'required|string|max:100',
            'district_code'    => 'nullable|string',
            'district_name'    => 'nullable|string|max:100',
            'village_code'     => 'nullable|string',
            'village_name'     => 'nullable|string|max:100',
            'alamat_detail'    => 'required|string|max:500',

            'link_gmaps'       => 'nullable|url|max:500',
            'foto_lokasi'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $user->update(['nama' => $validated['nama']]);

        $updateData = collect($validated)->except(['nama', 'foto_lokasi'])->toArray();

        if ($request->hasFile('foto_lokasi')) {
            if ($penerima->foto_lokasi && Storage::disk('public')->exists($penerima->foto_lokasi)) {
                Storage::disk('public')->delete($penerima->foto_lokasi);
            }
            $img = $request->file('foto_lokasi');
            $filename = time() . '_' . uniqid() . '.webp';
            $path = 'penerima_mbg/foto_lokasi/' . $filename;
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($img->getRealPath());
            if ($image->width() > 1200) {
                $image->scale(width: 1200);
            }
            Storage::disk('public')->put($path, (string) $image->toWebp(80));
            $updateData['foto_lokasi'] = $path;
        }

        $penerima->update($updateData);

        return redirect()->route('penerima-mbg.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
