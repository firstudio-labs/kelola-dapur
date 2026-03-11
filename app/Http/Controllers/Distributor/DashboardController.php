<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dapur = $user->userRole->dapur;
        $distributor = $user->distributor;
        
        return view('distributor.dashboard', compact('user', 'dapur', 'distributor'));
    }
}
