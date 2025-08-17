<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuMakananController extends Controller
{

    public function index(Request $request)
    {
        $query = MenuMakanan::with(['bahanMenu.templateItem']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status);
        }

        $menus = $query->orderBy('nama_menu', 'asc')->paginate(15);

        return view('kepaladapur.menu_makanan.index', compact('menus'));
    }

    public function show(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem', 'detailTransaksiDapur.transaksiDapur']);

        return view('kepaladapur.menu_makanan.show', compact('menuMakanan'));
    }

    public function getActiveMenus(Request $request)
    {
        $search = $request->get('search');

        $menus = MenuMakanan::active()
            ->when($search, function ($query, $search) {
                return $query->where('nama_menu', 'like', "%{$search}%");
            })
            ->select('id_menu', 'nama_menu', 'gambar_url')
            ->orderBy('nama_menu', 'asc')
            ->limit(20)
            ->get();

        return response()->json($menus);
    }

    public function checkStock(Request $request, MenuMakanan $menuMakanan)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'porsi' => 'required|integer|min:1|max:1000',
            'id_dapur' => 'required|exists:dapur,id_dapur'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Data tidak valid'], 400);
        }

        $user = Auth::user();
        // $allowedDapur = $user->dapurUsers()->pluck('id_dapur')->toArray();

        // if (!in_array($request->id_dapur, $allowedDapur)) {
        //     return response()->json(['error' => 'Tidak memiliki akses ke dapur ini'], 403);
        // }

        $stockAvailability = $menuMakanan->checkStockAvailability(
            $request->porsi,
            $request->id_dapur
        );

        return response()->json($stockAvailability);
    }
}
