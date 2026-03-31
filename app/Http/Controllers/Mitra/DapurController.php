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
                // Check if already bound
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

    public function destroy(MitraDapur $mitraDapur)
    {
        $user = Auth::user();
        $mitra = $user->mitra;

        if (!$mitra || $mitraDapur->id_mitra !== $mitra->id_mitra) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Cuma bisa hapus kalau belum di-approve, atau udah di-reject
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
