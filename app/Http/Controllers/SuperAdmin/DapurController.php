<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DapurController extends Controller
{
    public function dapurIndex()
    {
        $dapurList = Dapur::withCount(['kepalaDapur', 'adminGudang', 'ahliGizi'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('superadmin.dapur.index', compact('dapurList'));
    }

    public function dapurCreate()
    {
        return view('superadmin.dapur.create');
    }

    public function dapurStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur',
            'provinsi' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:14',
            'status' => 'required|in:active,inactive',
        ], [
            'nama_dapur.required' => 'Nama dapur harus diisi',
            'provinsi.required' => 'Provinsi harus dipilih',
            'kabupaten_kota.required' => 'Kabupaten/Kota harus dipilih',
            'alamat.required' => 'Alamat dapur harus diisi',
            'nama_dapur.unique' => 'Nama dapur sudah digunakan',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $wilayah = $request->kabupaten_kota . ', ' . $request->provinsi;

        Dapur::create([
            'nama_dapur' => $request->nama_dapur,
            'wilayah' => $wilayah,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'status' => $request->status,
        ]);

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
        $wilayahParts = explode(', ', $dapur->wilayah);
        $dapur->kabupaten_kota = $wilayahParts[0] ?? '';
        $dapur->provinsi = $wilayahParts[1] ?? '';

        return view('superadmin.dapur.edit', compact('dapur'));
    }

    public function dapurUpdate(Request $request, Dapur $dapur)
    {
        $validator = Validator::make($request->all(), [
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur,' . $dapur->id_dapur . ',id_dapur',
            'provinsi' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:14',
            'status' => 'required|in:active,inactive',
        ], [
            'nama_dapur.required' => 'Nama dapur harus diisi',
            'provinsi.required' => 'Provinsi harus dipilih',
            'kabupaten_kota.required' => 'Kabupaten/Kota harus dipilih',
            'alamat.required' => 'Alamat dapur harus diisi',
            'nama_dapur.unique' => 'Nama dapur sudah digunakan',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $wilayah = $request->kabupaten_kota . ', ' . $request->provinsi;

        $dapur->update([
            'nama_dapur' => $request->nama_dapur,
            'wilayah' => $wilayah,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'status' => $request->status,
        ]);

        return redirect()->route('superadmin.dapur.index')
            ->with('success', 'Dapur berhasil diperbarui');
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
}
