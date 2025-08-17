<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MenuMakanan;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuMakananController extends Controller
{
    public function index()
    {
        $menus = MenuMakanan::with(['bahanMenu.templateItem'])
            ->orderBy('nama_menu', 'asc')
            ->paginate(15);

        return view('superadmin.menu_makanan.index', compact('menus'));
    }

    public function create()
    {
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('superadmin.menu_makanan.create', compact('templateItems'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:100|unique:menu_makanan,nama_menu',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar_menu' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_active' => 'required|boolean',
            'bahan_menu' => 'required|array|min:1',
            'bahan_menu.*.id_template_item' => 'required|exists:template_items,id_template_item',
            'bahan_menu.*.jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'nama_menu.unique' => 'Nama menu sudah ada',
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

        $menu = MenuMakanan::create([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'gambar_menu' => $gambarMenu,
            'is_active' => $request->is_active
        ]);

        foreach ($request->bahan_menu as $bahan) {
            $menu->bahanMenu()->create([
                'id_template_item' => $bahan['id_template_item'],
                'jumlah_per_porsi' => $bahan['jumlah_per_porsi']
            ]);
        }

        return redirect()->route('superadmin.menu-makanan.index')
            ->with('success', 'Menu makanan berhasil ditambahkan');
    }

    public function show(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem', 'detailTransaksiDapur.transaksiDapur']);

        return view('superadmin.menu_makanan.show', compact('menuMakanan'));
    }

    public function edit(MenuMakanan $menuMakanan)
    {
        $menuMakanan->load(['bahanMenu.templateItem']);
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('superadmin.menu_makanan.edit', compact('menuMakanan', 'templateItems'));
    }

    public function update(Request $request, MenuMakanan $menuMakanan)
    {
        $validator = Validator::make($request->all(), [
            'nama_menu' => 'required|string|max:100|unique:menu_makanan,nama_menu,' . $menuMakanan->id_menu . ',id_menu',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar_menu' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'is_active' => 'required|boolean',
            'bahan_menu' => 'required|array|min:1',
            'bahan_menu.*.id_template_item' => 'required|exists:template_items,id_template_item',
            'bahan_menu.*.jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
        ], [
            'nama_menu.required' => 'Nama menu harus diisi',
            'nama_menu.unique' => 'Nama menu sudah ada',
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

        $menuMakanan->update([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'gambar_menu' => $gambarMenu,
            'is_active' => $request->is_active
        ]);

        $menuMakanan->bahanMenu()->delete();
        foreach ($request->bahan_menu as $bahan) {
            $menuMakanan->bahanMenu()->create([
                'id_template_item' => $bahan['id_template_item'],
                'jumlah_per_porsi' => $bahan['jumlah_per_porsi']
            ]);
        }

        return redirect()->route('superadmin.menu-makanan.index')
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

        return redirect()->route('superadmin.menu-makanan.index')
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
            ->select('id_menu', 'nama_menu', 'gambar_url')
            ->orderBy('nama_menu', 'asc')
            ->limit(20)
            ->get();

        return response()->json($menus);
    }
}
