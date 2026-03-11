<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\Dapur;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;

class PenerimaMbgController extends Controller
{
    public function index(Dapur $dapur, Request $request)
    {
        $status = $request->get('status', 'pending');

        $penerima = PenerimaMbg::where('id_dapur', $dapur->id_dapur)
            ->when($status !== 'semua', fn($q) => $q->where('status_approval', $status))
            ->with(['userRole.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending'  => PenerimaMbg::where('id_dapur', $dapur->id_dapur)->where('status_approval', 'pending')->count(),
            'approved' => PenerimaMbg::where('id_dapur', $dapur->id_dapur)->where('status_approval', 'approved')->count(),
            'rejected' => PenerimaMbg::where('id_dapur', $dapur->id_dapur)->where('status_approval', 'rejected')->count(),
        ];

        return view('kepaladapur.penerima_mbg.index', compact('dapur', 'penerima', 'status', 'counts'));
    }

    public function show(Dapur $dapur, PenerimaMbg $penerima_mbg)
    {
        if ($penerima_mbg->id_dapur != $dapur->id_dapur) {
            return redirect()->route('kepala-dapur.penerima-mbg.index', $dapur)
                ->with('error', 'Data penerima tidak ditemukan di dapur ini.');
        }

        $penerima_mbg->load('userRole.user');
        return view('kepaladapur.penerima_mbg.show', compact('dapur', 'penerima_mbg'));
    }

    public function approve(Dapur $dapur, PenerimaMbg $penerima_mbg, Request $request)
    {
        if ($penerima_mbg->id_dapur != $dapur->id_dapur) {
            return redirect()->route('kepala-dapur.penerima-mbg.index', $dapur)
                ->with('error', 'Data penerima tidak ditemukan di dapur ini.');
        }

        $penerima_mbg->update([
            'status_approval'  => 'approved',
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->route('kepala-dapur.penerima-mbg.index', $dapur)
            ->with('success', 'Pengajuan penerima MBG berhasil disetujui.');
    }

    public function reject(Dapur $dapur, PenerimaMbg $penerima_mbg, Request $request)
    {
        if ($penerima_mbg->id_dapur != $dapur->id_dapur) {
            return redirect()->route('kepala-dapur.penerima-mbg.index', $dapur)
                ->with('error', 'Data penerima tidak ditemukan di dapur ini.');
        }

        $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ], [
            'catatan_approval.required' => 'Alasan penolakan harus diisi',
        ]);

        $penerima_mbg->update([
            'status_approval'  => 'rejected',
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->route('kepala-dapur.penerima-mbg.index', $dapur)
            ->with('success', 'Pengajuan penerima MBG telah ditolak.');
    }
}
