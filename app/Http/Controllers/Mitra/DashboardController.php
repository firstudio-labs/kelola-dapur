<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mitra = $user->mitra;
        
        if (!$mitra) {
            return redirect()->route('login')->with('error', 'Data mitra tidak ditemukan.');
        }

        // Ambil statistik untuk dashboard
        $stats = [
            'total' => $mitra->mitraDapur()->count(),
            'approved' => $mitra->mitraDapur()->where('status', 'approved')->count(),
            'pending' => $mitra->mitraDapur()->where('status', 'pending')->count(),
            'rejected' => $mitra->mitraDapur()->where('status', 'rejected')->count(),
        ];

        // Aktivitas terbaru (optional, let's just get 5 latest dapur interactions)
        $recentActivities = $mitra->mitraDapur()->with('dapur')->latest()->limit(5)->get();

        return view('mitra.dashboard.index', compact('stats', 'recentActivities'));
    }
}
