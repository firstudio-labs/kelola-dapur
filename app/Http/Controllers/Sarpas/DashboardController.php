<?php

namespace App\Http\Controllers\Sarpas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $dapur = $request->current_dapur;
            
            // Similar to Ahli Gizi's dashboard setup.
            return view('sarpas.dashboard', compact('user', 'dapur'));
        } catch (Exception $e) {
            Log::error('Dashboard Error', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }
}
