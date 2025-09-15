<?php

namespace App\Http\Controllers\KepalaDapur;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuMakananController extends Controller
{

    public function index(Request $request)
    {
        // Base query untuk filter (tanpa with, untuk efisiensi count)
        $baseQuery = MenuMakanan::query();

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $baseQuery->where('is_active', $request->status);
        }

        // Dapur filter
        if ($request->has('dapur') && $request->dapur !== 'all' && !empty($request->dapur)) {
            $baseQuery->where('created_by_dapur_id', $request->dapur);
        }

        // Kategori filter
        if ($request->has('kategori') && $request->kategori !== 'all' && !empty($request->kategori)) {
            $baseQuery->where('kategori', $request->kategori);
        }

        // Query untuk menus dengan relations (untuk display)
        $menuQuery = clone $baseQuery;
        $menuQuery->with(['bahanMenu.templateItem', 'createdByDapur']);
        $menus = $menuQuery->orderBy('nama_menu', 'asc')->paginate(15);

        // Hitung statistik dari baseQuery (total keseluruhan, bukan per halaman)
        $totalMenus = $baseQuery->count();
        $activeMenus = $baseQuery->clone()->where('is_active', true)->count();
        $inactiveMenus = $baseQuery->clone()->where('is_active', false)->count();

        // Category statistics
        $kategoriStats = [
            'Karbohidrat' => $baseQuery->clone()->where('kategori', 'Karbohidrat')->count(),
            'Lauk' => $baseQuery->clone()->where('kategori', 'Lauk')->count(),
            'Sayur' => $baseQuery->clone()->where('kategori', 'Sayur')->count(),
            'Tambahan' => $baseQuery->clone()->where('kategori', 'Tambahan')->count(),
        ];

        // Get all dapur for filter dropdown
        $dapurs = Dapur::select('id_dapur', 'nama_dapur')
            ->where('status', 'active')
            ->orderBy('nama_dapur', 'asc')
            ->get();

        return view('kepaladapur.menu_makanan.index', compact(
            'menus',
            'dapurs',
            'totalMenus',
            'activeMenus',
            'inactiveMenus',
            'kategoriStats'
        ));
    }

    public function show(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem', 'detailTransaksiDapur.transaksiDapur', 'createdByDapur']);

        return view('kepaladapur.menu_makanan.show', compact('menuMakanan'));
    }

    public function getActiveMenus(Request $request)
    {
        $search = $request->get('search');
        $dapurId = $request->get('dapur_id');

        $query = MenuMakanan::active();

        // Filter by dapur if provided
        if ($dapurId) {
            $query->where('created_by_dapur_id', $dapurId);
        }

        // Search filter
        if ($search) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        $menus = $query->select('id_menu', 'nama_menu', 'gambar_menu', 'kategori')
            ->with('createdByDapur:id_dapur,nama_dapur')
            ->orderBy('nama_menu', 'asc')
            ->limit(20)
            ->get();

        // Add gambar_url attribute to each menu
        $menus->each(function ($menu) {
            $menu->gambar_url = $menu->gambar_url;
        });

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

        // Check if user has access to the specified dapur
        // $allowedDapur = $user->dapurUsers()->pluck('id_dapur')->toArray();
        // if (!in_array($request->id_dapur, $allowedDapur)) {
        //     return response()->json(['error' => 'Tidak memiliki akses ke dapur ini'], 403);
        // }

        // Check stock availability (you'll need to implement this method in MenuMakanan model)
        $stockAvailability = $menuMakanan->checkStockAvailability(
            $request->porsi,
            $request->id_dapur
        );

        return response()->json($stockAvailability);
    }

    /**
     * Get menu statistics for dashboard or reports
     */
    public function getMenuStatistics(Request $request)
    {
        $dapurId = $request->get('dapur_id');

        $query = MenuMakanan::query();

        if ($dapurId) {
            $query->where('created_by_dapur_id', $dapurId);
        }

        $statistics = [
            'total' => $query->count(),
            'active' => $query->where('is_active', true)->count(),
            'inactive' => $query->where('is_active', false)->count(),
            'by_category' => [
                'Karbohidrat' => $query->where('kategori', 'Karbohidrat')->count(),
                'Lauk' => $query->where('kategori', 'Lauk')->count(),
                'Sayur' => $query->where('kategori', 'Sayur')->count(),
                'Tambahan' => $query->where('kategori', 'Tambahan')->count(),
            ]
        ];

        return response()->json($statistics);
    }

    /**
     * Get menu options for select2 or similar dropdowns
     */
    public function getMenuOptions(Request $request)
    {
        $search = $request->get('q', '');
        $dapurId = $request->get('dapur_id');

        $query = MenuMakanan::active();

        if ($dapurId) {
            $query->where('created_by_dapur_id', $dapurId);
        }

        if ($search) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        $menus = $query->select('id_menu as id', 'nama_menu as text', 'kategori')
            ->orderBy('nama_menu', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $menus
        ]);
    }
}
