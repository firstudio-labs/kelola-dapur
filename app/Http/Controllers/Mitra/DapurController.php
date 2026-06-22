<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\MitraDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DapurController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra;
        if (!$mitra) {
            return redirect()->route('login')->with('error', 'Data mitra tidak ditemukan.');
        }

        $mitraDapurList = $mitra->mitraDapur()->with('dapur')->latest()->get();

        return view('mitra.dapur.index', compact('mitraDapurList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_dapur'   => 'required|array|min:1',
            'id_dapur.*' => 'exists:dapur,id_dapur',
        ]);

        $user = Auth::user();
        $mitra = $user->mitra;

        if (!$mitra) {
            return back()->with('error', 'Data mitra tidak ditemukan.');
        }

        try {
            DB::beginTransaction();

            $addedCount = 0;
            $skippedCount = 0;

            foreach ($request->id_dapur as $dapurId) {
                $existing = MitraDapur::where('id_mitra', $mitra->id_mitra)
                    ->where('id_dapur', $dapurId)
                    ->first();

                if ($existing) {
                    $skippedCount++;
                    continue;
                }

                MitraDapur::create([
                    'id_mitra' => $mitra->id_mitra,
                    'id_dapur' => $dapurId,
                    'status'   => 'pending',
                ]);
                $addedCount++;
            }

            DB::commit();

            if ($addedCount > 0) {
                $msg = "Berhasil mengajukan {$addedCount} dapur baru.";
                if ($skippedCount > 0) {
                    $msg .= " ({$skippedCount} dapur dilewati karena sudah ada di daftar).";
                }
                return back()->with('success', $msg);
            }

            return back()->with('info', 'Semua dapur yang dipilih sudah ada di daftar Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menambahkan dapur bulk untuk mitra', [
                'mitra_id' => $mitra->id_mitra,
                'dapur_ids' => $request->id_dapur,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem saat memproses pengajuan.');
        }
    }

    public function show(Request $request, MitraDapur $mitraDapur)
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        if (!$mitra || $mitraDapur->id_mitra !== $mitra->id_mitra) {
            return redirect()->route('mitra.dapur.index')->with('error', 'Akses ditolak.');
        }

        if (!$mitraDapur->isApproved()) {
            return redirect()->route('mitra.dapur.index')->with('error', 'Dapur belum disetujui, tidak dapat melihat detail.');
        }

        $dapur = $mitraDapur->dapur;
        $dapur->load([
            'kepalaDapur.userRole.user',
            'adminGudang.userRole.user',
            'ahliGizi.userRole.user',
            'distributor.userRole.user',
            'stockItems.templateItem',
            'prasarana.item',
            'prasarana.fotos',
            'subscriptionRequests.package'
        ]);
        
        $penerimaMbgList = \App\Models\PenerimaMbg::where('id_dapur', $dapur->id_dapur)->with('userRole.user')->get();
        $userRoles = \App\Models\UserRole::where('id_dapur', $dapur->id_dapur)->with('user')->get();
        $produksiList = \App\Models\Produksi::where('id_dapur', $dapur->id_dapur)->with('userRole.user')->get();
        $menus = \App\Models\MenuMakanan::byDapur($dapur->id_dapur)->with('bahanMenu.templateItem')->get();
        $kategoriPrasarana = \App\Models\KategoriPrasarana::with('items')->where('is_active', true)->get();

        $staffMembers = collect();
        if ($dapur->kepalaDapur) {
            foreach ($dapur->kepalaDapur as $kd) {
                $staffMembers->push([
                    'id' => $kd->id_kepala_dapur ?? $kd->id,
                    'nama' => $kd->nama_lengkap,
                    'email' => $kd->userRole->user->email ?? '-',
                    'role' => 'Kepala Dapur',
                    'role_filter' => 'Kepala Dapur',
                    'nik' => $kd->nik_kepala_sppg ?? '-',
                    'wa' => $kd->kontak_wa ?? '-',
                    'pendidikan' => $kd->pendidikan_terakhir ?? '-',
                    'province_name' => $kd->province_name ?? '-',
                    'regency_name' => $kd->regency_name ?? '-',
                    'district_name' => $kd->district_name ?? '-',
                    'village_name' => $kd->village_name ?? '-',
                    'alamat_detail' => $kd->alamat_detail ?? '-',
                    'foto_diri' => $kd->foto_diri ? asset('storage/' . str_replace('storage/', '', $kd->foto_diri)) : null,
                ]);
            }
        }
        if ($dapur->ahliGizi) {
            foreach ($dapur->ahliGizi as $ag) {
                $staffMembers->push([
                    'id' => $ag->id_ahli_gizi ?? $ag->id,
                    'nama' => $ag->nama_lengkap,
                    'email' => $ag->userRole->user->email ?? '-',
                    'role' => 'Ahli Gizi',
                    'role_filter' => 'Ahli Gizi',
                    'nik' => $ag->nik_ahli_gizi ?? '-',
                    'wa' => $ag->kontak_wa ?? '-',
                    'pendidikan' => $ag->pendidikan ?? '-',
                    'province_name' => $ag->province_name ?? '-',
                    'regency_name' => $ag->regency_name ?? '-',
                    'district_name' => $ag->district_name ?? '-',
                    'village_name' => $ag->village_name ?? '-',
                    'alamat_detail' => $ag->alamat_detail ?? '-',
                    'foto_diri' => $ag->foto_diri ? asset('storage/' . str_replace('storage/', '', $ag->foto_diri)) : null,
                ]);
            }
        }
        if ($dapur->adminGudang) {
            foreach ($dapur->adminGudang as $ad) {
                $staffMembers->push([
                    'id' => $ad->id_admin_gudang ?? $ad->id,
                    'nama' => $ad->nama_lengkap,
                    'email' => $ad->userRole->user->email ?? '-',
                    'role' => 'Admin Gudang',
                    'role_filter' => 'Admin Gudang',
                    'nik' => $ad->nik_admin_gudang ?? '-',
                    'wa' => $ad->kontak_wa ?? '-',
                    'pendidikan' => $ad->pendidikan ?? '-',
                    'province_name' => $ad->province_name ?? '-',
                    'regency_name' => $ad->regency_name ?? '-',
                    'district_name' => $ad->district_name ?? '-',
                    'village_name' => $ad->village_name ?? '-',
                    'alamat_detail' => $ad->alamat_detail ?? '-',
                    'foto_diri' => $ad->foto_diri ? asset('storage/' . str_replace('storage/', '', $ad->foto_diri)) : null,
                ]);
            }
        }
        $akuntanList = \App\Models\Akuntan::where('id_dapur', $dapur->id_dapur)->with('userRole.user')->get();
        if ($akuntanList) {
            foreach ($akuntanList as $ak) {
                $staffMembers->push([
                    'id' => $ak->id_akuntan ?? $ak->id,
                    'nama' => $ak->nama_lengkap,
                    'email' => $ak->userRole->user->email ?? '-',
                    'role' => 'Akuntan',
                    'role_filter' => 'Akuntan',
                    'nik' => $ak->nik_akuntan ?? '-',
                    'wa' => $ak->kontak_wa ?? '-',
                    'pendidikan' => $ak->pendidikan ?? '-',
                    'province_name' => $ak->province_name ?? '-',
                    'regency_name' => $ak->regency_name ?? '-',
                    'district_name' => $ak->district_name ?? '-',
                    'village_name' => $ak->village_name ?? '-',
                    'alamat_detail' => $ak->alamat_detail ?? '-',
                    'foto_diri' => $ak->foto_diri ? asset('storage/' . str_replace('storage/', '', $ak->foto_diri)) : null,
                ]);
            }
        }
        if ($produksiList) {
            foreach ($produksiList as $pr) {
                $staffMembers->push([
                    'id' => $pr->id_produksi ?? $pr->id,
                    'nama' => $pr->nama_lengkap,
                    'email' => $pr->userRole->user->email ?? '-',
                    'role' => 'Tim Produksi (' . ($pr->jabatan ?? '-') . ')',
                    'role_filter' => 'Tim Produksi',
                    'nik' => $pr->nik_produksi ?? '-',
                    'wa' => $pr->kontak_wa ?? '-',
                    'pendidikan' => $pr->pendidikan ?? '-',
                    'province_name' => $pr->province_name ?? '-',
                    'regency_name' => $pr->regency_name ?? '-',
                    'district_name' => $pr->district_name ?? '-',
                    'village_name' => $pr->village_name ?? '-',
                    'alamat_detail' => $pr->alamat_detail ?? '-',
                    'foto_diri' => $pr->foto_diri ? asset('storage/' . str_replace('storage/', '', $pr->foto_diri)) : null,
                ]);
            }
        }
        if ($dapur->distributor) {
            foreach ($dapur->distributor as $db) {
                $staffMembers->push([
                    'id' => $db->id_distributor ?? $db->id,
                    'nama' => $db->nama_lengkap,
                    'email' => $db->userRole->user->email ?? '-',
                    'role' => 'Distributor (' . ($db->jabatan ?? '-') . ')',
                    'role_filter' => 'Distributor',
                    'nik' => $db->nik_distributor ?? '-',
                    'wa' => $db->kontak_wa ?? '-',
                    'pendidikan' => $db->pendidikan ?? '-',
                    'province_name' => $db->province_name ?? '-',
                    'regency_name' => $db->regency_name ?? '-',
                    'district_name' => $db->district_name ?? '-',
                    'village_name' => $db->village_name ?? '-',
                    'alamat_detail' => $db->alamat_detail ?? '-',
                    'foto_diri' => $db->foto_diri ? asset('storage/' . str_replace('storage/', '', $db->foto_diri)) : null,
                ]);
            }
        }
        
        return view('mitra.dapur.show', compact('mitraDapur', 'dapur', 'menus', 'penerimaMbgList', 'userRoles', 'produksiList', 'kategoriPrasarana', 'staffMembers'));
    }

    public function destroy(MitraDapur $mitraDapur)
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        if (!$mitra || $mitraDapur->id_mitra !== $mitra->id_mitra) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($mitraDapur->isApproved()) {
            return back()->with('error', 'Tidak dapat menghapus dapur yang sudah disetujui. Hubungi Kepala Dapur.');
        }

        try {
            $mitraDapur->delete();
            return back()->with('success', 'Pengajuan dapur berhasil dibatalkan/dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus pengajuan dapur mitra', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
