<?php

namespace App\Http\Controllers\PenerimaMbg;

use App\Http\Controllers\Controller;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PorsiController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)->firstOrFail();

        return view('penerima_mbg.porsi.edit', compact('user', 'penerima'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)->firstOrFail();

        if ($penerima->status_approval !== 'approved') {
            return redirect()->back()
                ->with('error', 'Pengajuan Anda belum disetujui. Porsi MBG hanya dapat diubah setelah disetujui oleh Dapur SPPG.');
        }

        $validated = $request->validate([
            'jumlah_porsi' => 'required|integer|min:1|max:9999999',
        ], [
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi',
            'jumlah_porsi.integer'  => 'Jumlah porsi harus berupa angka bulat',
            'jumlah_porsi.min'      => 'Jumlah porsi minimal 1',
            'jumlah_porsi.max'      => 'Jumlah porsi maksimal 9.999.999',
        ]);

        $penerima->update(['jumlah_porsi' => $validated['jumlah_porsi']]);

        return redirect()->route('penerima-mbg.dashboard')
            ->with('success', 'Jumlah porsi MBG berhasil diperbarui menjadi ' . $validated['jumlah_porsi'] . ' porsi.');
    }
}
