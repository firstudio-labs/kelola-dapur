<?php

namespace App\Http\Controllers\PenerimaMbg;

use App\Http\Controllers\Controller;
use App\Models\PenerimaMbg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penerima = PenerimaMbg::where('id_user_role', $user->userRole->id_user_role)
            ->with('dapur')
            ->first();

        return view('penerima_mbg.dashboard', compact('user', 'penerima'));
    }
}
