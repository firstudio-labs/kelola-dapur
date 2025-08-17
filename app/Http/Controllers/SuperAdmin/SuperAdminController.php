<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dapur;
use App\Models\SuperAdmin;
use App\Models\KepalaDapur;
use App\Models\AdminGudang;
use App\Models\AhliGizi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'super.admin.only']);
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_dapur' => Dapur::count(),
            'active_dapur' => Dapur::where('status', 'active')->count(),
            'inactive_dapur' => Dapur::where('status', 'inactive')->count(),
            'total_kepala_dapur' => KepalaDapur::count(),
            'total_admin_gudang' => AdminGudang::count(),
            'total_ahli_gizi' => AhliGizi::count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_dapur = Dapur::latest()->take(5)->get();

        return view('superadmin.dashboard.index', compact('stats', 'recent_users', 'recent_dapur'));
    }
}
