<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        return view('superadmin.dapur.edit', compact('dapur'));
    }

    public function dapurUpdate(Request $request, Dapur $dapur)
    {
        $validator = Validator::make($request->all(), [
            'nama_dapur' => 'required|string|max:255|unique:dapur,nama_dapur,' . $dapur->id_dapur . ',id_dapur',
            'provinsi' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'status' => 'required|in:active,inactive',
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Extract wilayah data from hidden form fields or API lookup
            $wilayahData = $this->extractWilayahDataForUpdate($request, $dapur);

            // Update dapur dengan data yang sudah divalidasi
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
            ]);

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
        // If codes are sent via hidden inputs (recommended approach)
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
        // Check if we  new codes from the form
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
