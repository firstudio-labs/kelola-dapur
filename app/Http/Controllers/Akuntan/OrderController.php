<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dapur = $user->userRole->dapur;
        
        $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $stats = [
            'belum_dibuat' => 0,
            'sedang_dibuat' => 0,
            'selesai' => 0
        ];
        $statusFilter = 'all';
        
        return view('akuntan.order.index', compact('user', 'dapur', 'orders', 'stats', 'statusFilter'));
    }

    public function show($id)
    {
        return redirect()->back();
    }
}
