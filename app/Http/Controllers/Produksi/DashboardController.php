<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dapur = $user->userRole->dapur;
        
        return view('produksi.dashboard', compact('user', 'dapur'));
    }
}
