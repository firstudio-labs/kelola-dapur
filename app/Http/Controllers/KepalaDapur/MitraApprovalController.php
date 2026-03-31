<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\MitraDapur;
use App\Models\Dapur;
use App\Models\KelolaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MitraApprovalController extends Controller
{
    public function index(Dapur $dapur)
    {
        $user = Auth::user();
        if (!$user->isKepalaDapur()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $id_dapur = $dapur->id_dapur;

        // Ambil semua request dari mitra ke dapur ini
        $mitraDapurList = MitraDapur::where('id_dapur', $id_dapur)
            ->with('mitra')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->get();

        return view('kepala_dapur.mitra_approval.index', compact('mitraDapurList', 'dapur'));
    }

    public function show(Dapur $dapur, MitraDapur $mitraDapur)
    {
        if ($mitraDapur->id_dapur != $dapur->id_dapur) {
            return redirect()->route('kepala-dapur.mitra-approval.index', ['dapur' => $dapur->id_dapur])->with('error', 'Akses ditolak.');
        }

        $mitraDapur->load('mitra.user');

        return view('kepala_dapur.mitra_approval.show', compact('mitraDapur', 'dapur'));
    }

    public function approve(Dapur $dapur, MitraDapur $mitraDapur, Request $request)
    {
        if ($mitraDapur->id_dapur != $dapur->id_dapur || $mitraDapur->status !== 'pending') {
            return back()->with('error', 'Data pengajuan ini tidak valid untuk disetujui.');
        }

        try {
            $mitraDapur->update([
                'status'      => 'approved',
                'catatan'     => $request->catatan,
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('kepala-dapur.mitra-approval.index', ['dapur' => $dapur->id_dapur])->with('success', 'Berhasil menyetujui pendaftaran Mitra.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve Mitra Error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menyetujui Mitra. Hubungi admin.');
        }
    }

    public function reject(Dapur $dapur, MitraDapur $mitraDapur, Request $request)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        if ($mitraDapur->id_dapur != $dapur->id_dapur || $mitraDapur->status !== 'pending') {
            return back()->with('error', 'Data pengajuan ini tidak valid untuk ditolak.');
        }

        try {
            $mitraDapur->update([
                'status'  => 'rejected',
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('kepala-dapur.mitra-approval.index', ['dapur' => $dapur->id_dapur])->with('success', 'Berhasil menolak pendaftaran Mitra.');
        } catch (\Exception $e) {
            Log::error('Reject Mitra Error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menolak Mitra. Hubungi admin.');
        }
    }
}
