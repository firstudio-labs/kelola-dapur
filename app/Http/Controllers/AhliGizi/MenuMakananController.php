<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MenuMakananController extends Controller
{
    public function index()
    {
        $menus = MenuMakanan::with(['bahanMenu.templateItem', 'createdByDapur'])
            ->orderBy('nama_menu', 'asc')
            ->paginate(15);

        return view('ahligizi.menu_makanan.index', compact('menus'));
    }

    public function create()
    {
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();
        $currentDapur = Auth::user()->userRole->dapur ?? null;

        return view('ahligizi.menu_makanan.create', compact('templateItems', 'currentDapur'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:100|unique:menu_makanan,nama_menu',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori' => 'required|in:Karbohidrat,Lauk,Sayur,Tambahan',
            'gambar_menu' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_active' => 'required|boolean',
            'bahan_menu' => 'required|array|min:1',
            'bahan_menu.*.id_template_item' => 'required|exists:template_items,id_template_item',
            'bahan_menu.*.jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
            'bahan_menu.*.is_bahan_basah' => 'nullable|boolean',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'nama_menu.unique' => 'Nama menu sudah ada',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.in' => 'Kategori tidak valid',
            'gambar_menu.image' => 'File harus berupa gambar',
            'gambar_menu.max' => 'Ukuran gambar maksimal 2MB',
            'bahan_menu.required' => 'Minimal harus ada 1 bahan menu',
            'bahan_menu.*.id_template_item.required' => 'Template bahan harus dipilih',
            'bahan_menu.*.jumlah_per_porsi.required' => 'Jumlah per porsi harus diisi',
            'bahan_menu.*.jumlah_per_porsi.min' => 'Jumlah per porsi minimal 0.0001',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gambarMenu = null;
        if ($request->hasFile('gambar_menu')) {
            $file = $request->file('gambar_menu');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/menu', $filename);
            $gambarMenu = $filename;
        }

        $createdByDapurId = Auth::user()->userRole->id_dapur ?? null;
        Log::info('Store Menu: created_by_dapur_id', ['id' => $createdByDapurId]);

        if (!$createdByDapurId) {
            return redirect()->back()
                ->withErrors(['error' => 'Dapur tidak ditemukan untuk pengguna ini'])
                ->withInput();
        }

        $menu = MenuMakanan::create([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'gambar_menu' => $gambarMenu,
            'is_active' => $request->is_active,
            'created_by_dapur_id' => $createdByDapurId,
        ]);

        foreach ($request->bahan_menu as $bahan) {
            $menu->bahanMenu()->create([
                'id_template_item' => $bahan['id_template_item'],
                'jumlah_per_porsi' => $bahan['jumlah_per_porsi'],
                'is_bahan_basah' => isset($bahan['is_bahan_basah']) ? (bool)$bahan['is_bahan_basah'] : false
            ]);
        }

        return redirect()->route('ahli-gizi.menu-makanan.index')
            ->with('success', 'Menu makanan berhasil ditambahkan');
    }

    public function show(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem', 'detailTransaksiDapur.transaksiDapur.dapur', 'createdByDapur']);

        return view('ahligizi.menu_makanan.show', compact('menuMakanan'));
    }

    public function edit(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem']);
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();
        $currentDapur = Auth::user()->userRole->dapur ?? null;

        return view('ahligizi.menu_makanan.edit', compact('menuMakanan', 'templateItems', 'currentDapur'));
    }

    public function update(Request $request, MenuMakanan $menuMakanan)
    {
        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:100|unique:menu_makanan,nama_menu,' . $menuMakanan->id_menu . ',id_menu',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori' => 'required|in:Karbohidrat,Lauk,Sayur,Tambahan',
            'gambar_menu' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_active' => 'required|boolean',
            'bahan_menu' => 'required|array|min:1',
            'bahan_menu.*.id_template_item' => 'required|exists:template_items,id_template_item',
            'bahan_menu.*.jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
            'bahan_menu.*.is_bahan_basah' => 'nullable|boolean',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'nama_menu.unique' => 'Nama menu sudah ada',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.in' => 'Kategori tidak valid',
            'gambar_menu.image' => 'File harus berupa gambar',
            'gambar_menu.max' => 'Ukuran gambar maksimal 2MB',
            'bahan_menu.required' => 'Minimal harus ada 1 bahan menu',
            'bahan_menu.*.id_template_item.required' => 'Template bahan harus dipilih',
            'bahan_menu.*.jumlah_per_porsi.required' => 'Jumlah per porsi harus diisi',
            'bahan_menu.*.jumlah_per_porsi.min' => 'Jumlah per porsi minimal 0.0001',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gambarMenu = $menuMakanan->gambar_menu;
        if ($request->hasFile('gambar_menu')) {
            if ($menuMakanan->gambar_menu) {
                $menuMakanan->deleteGambar();
            }

            $file = $request->file('gambar_menu');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/menu', $filename);
            $gambarMenu = $filename;
        }

        $createdByDapurId = Auth::user()->userRole->id_dapur ?? null;
        Log::info('Update Menu: created_by_dapur_id', ['id' => $createdByDapurId]);

        if (!$createdByDapurId) {
            return redirect()->back()
                ->withErrors(['error' => 'Dapur tidak ditemukan untuk pengguna ini'])
                ->withInput();
        }

        $menuMakanan->update([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'gambar_menu' => $gambarMenu,
            'is_active' => $request->is_active,
            'created_by_dapur_id' => $createdByDapurId,
        ]);

        $menuMakanan->bahanMenu()->delete();
        foreach ($request->bahan_menu as $bahan) {
            $menuMakanan->bahanMenu()->create([
                'id_template_item' => $bahan['id_template_item'],
                'jumlah_per_porsi' => $bahan['jumlah_per_porsi'],
                'is_bahan_basah' => isset($bahan['is_bahan_basah']) ? (bool)$bahan['is_bahan_basah'] : false
            ]);
        }

        return redirect()->route('ahli-gizi.menu-makanan.index')
            ->with('success', 'Menu makanan berhasil diperbarui');
    }

    public function destroy(MenuMakanan $menuMakanan)
    {
        if ($menuMakanan->detailTransaksiDapur()->exists()) {
            return redirect()->back()
                ->with('error', 'Menu tidak dapat dihapus karena sudah digunakan dalam transaksi');
        }

        if ($menuMakanan->gambar_menu) {
            $menuMakanan->deleteGambar();
        }

        $menuMakanan->delete();

        return redirect()->route('ahli-gizi.menu-makanan.index')
            ->with('success', 'Menu makanan berhasil dihapus');
    }

    public function toggleStatus(MenuMakanan $menuMakanan)
    {
        $menuMakanan->update([
            'is_active' => !$menuMakanan->is_active
        ]);

        $status = $menuMakanan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Menu berhasil {$status}");
    }

    public function checkStock(Request $request, MenuMakanan $menuMakanan)
    {
        $validator = Validator::make($request->all(), [
            'porsi' => 'required|integer|min:1|max:1000',
            'id_dapur' => 'required|exists:dapur,id_dapur'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Data tidak valid'], 400);
        }

        $stockAvailability = $menuMakanan->checkStockAvailability(
            $request->porsi,
            $request->id_dapur
        );

        return response()->json($stockAvailability);
    }

    public function getActiveMenus(Request $request)
    {
        $search = $request->get('search');

        $menus = MenuMakanan::active()
            ->when($search, function ($query, $search) {
                return $query->where('nama_menu', 'like', "%{$search}%");
            })
            ->select('id_menu', 'nama_menu', 'gambar_menu', 'deskripsi')
            ->orderBy('nama_menu', 'asc')
            ->limit(20)
            ->get();

        $formattedMenus = $menus->map(function ($menu) {
            return [
                'id_menu' => $menu->id_menu,
                'nama_menu' => $menu->nama_menu,
                'gambar_url' => $menu->gambar_url,
                'deskripsi' => $menu->deskripsi
            ];
        });

        return response()->json($formattedMenus);
    }

    public function detail($id)
    {
        try {
            $menu = MenuMakanan::with(['bahanMenu.templateItem'])
                ->find($id);

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak ditemukan'
                ], 404);
            }

            if (!$menu->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak aktif'
                ], 400);
            }

            $menuData = [
                'id_menu' => $menu->id_menu,
                'nama_menu' => $menu->nama_menu,
                'gambar' => $menu->gambar_url,
                'deskripsi' => $menu->deskripsi,
                'kategori' => $menu->kategori,
                'is_active' => $menu->is_active,
                'bahan_menu' => $menu->bahanMenu->map(function ($bahan) {
                    return [
                        'id_bahan_menu' => $bahan->id_bahan_menu,
                        'id_template_item' => $bahan->id_template_item,
                        'nama_bahan' => $bahan->templateItem->nama_bahan,
                        'jumlah_per_porsi' => $bahan->jumlah_per_porsi,
                        'satuan' => $bahan->templateItem->satuan,
                        'is_bahan_basah' => $bahan->is_bahan_basah,
                        'template_item' => [
                            'id_template_item' => $bahan->templateItem->id_template_item,
                            'nama_bahan' => $bahan->templateItem->nama_bahan,
                            'satuan' => $bahan->templateItem->satuan,
                            'keterangan' => $bahan->templateItem->keterangan
                        ]
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'menu' => $menuData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getIngredientDetails(MenuMakanan $menu)
    {
        try {
            $menu->load(['bahanMenu.templateItem']);

            if (!$menu->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak aktif'
                ], 400);
            }

            $bahanMenu = $menu->bahanMenu->map(function ($bahan) {
                return [
                    'id_bahan_menu' => $bahan->id_bahan_menu,
                    'id_template_item' => $bahan->id_template_item,
                    'nama_bahan' => $bahan->templateItem ? $bahan->templateItem->nama_bahan : 'Unknown',
                    'jumlah_per_porsi' => (float) $bahan->jumlah_per_porsi,
                    'satuan' => $bahan->templateItem ? $bahan->templateItem->satuan : '',
                    'is_bahan_basah' => $bahan->is_bahan_basah
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'bahan_menu' => $bahanMenu
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getIngredientDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
