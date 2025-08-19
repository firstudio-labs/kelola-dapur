<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BahanMenu;
use App\Models\MenuMakanan;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BahanMenuController extends Controller
{
    public function index()
    {
        $bahanMenus = BahanMenu::with(['menuMakanan', 'templateItem'])
            ->orderBy('id_menu', 'asc')
            ->paginate(15);

        return view('bahan-menu.index', compact('bahanMenus'));
    }

    public function indexByMenu(MenuMakanan $menu)
    {
        $bahanMenus = BahanMenu::with(['templateItem'])
            ->where('id_menu', $menu->id_menu)
            ->orderBy('id_template_item', 'asc')
            ->paginate(15);

        return view('bahan-menu.index-by-menu', compact('bahanMenus', 'menu'));
    }

    public function create(Request $request)
    {
        $menuId = $request->get('menu_id');
        $menu = null;

        if ($menuId) {
            $menu = MenuMakanan::findOrFail($menuId);
        }

        $menus = MenuMakanan::orderBy('nama_menu', 'asc')->get();
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('bahan-menu.create', compact('menus', 'templateItems', 'menu'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_menu' => 'required|exists:menu_makanan,id_menu',
            'id_template_item' => 'required|exists:template_items,id_template_item',
            'jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
        ], [
            'id_menu.required' => 'Menu harus dipilih',
            'id_template_item.required' => 'Template bahan harus dipilih',
            'jumlah_per_porsi.required' => 'Jumlah per porsi harus diisi',
            'jumlah_per_porsi.min' => 'Jumlah per porsi minimal 0.0001',
        ]);

        $exists = BahanMenu::where('id_menu', $request->id_menu)
            ->where('id_template_item', $request->id_template_item)
            ->exists();

        if ($exists) {
            $validator->after(function ($validator) {
                $validator->errors()->add('id_template_item', 'Bahan ini sudah ditambahkan ke menu tersebut');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BahanMenu::create([
            'id_menu' => $request->id_menu,
            'id_template_item' => $request->id_template_item,
            'jumlah_per_porsi' => $request->jumlah_per_porsi
        ]);

        return redirect()->route('bahan-menu.index-by-menu', ['menu' => $request->id_menu])
            ->with('success', 'Bahan menu berhasil ditambahkan');
    }

    public function show(BahanMenu $bahanMenu)
    {
        $bahanMenu->load(['menuMakanan', 'templateItem']);

        return view('bahan-menu.show', compact('bahanMenu'));
    }

    public function edit(BahanMenu $bahanMenu)
    {
        $bahanMenu->load(['menuMakanan', 'templateItem']);
        $menus = MenuMakanan::orderBy('nama_menu', 'asc')->get();
        $templateItems = TemplateItem::orderBy('nama_bahan', 'asc')->get();

        return view('bahan-menu.edit', compact('bahanMenu', 'menus', 'templateItems'));
    }

    public function update(Request $request, BahanMenu $bahanMenu)
    {
        $validator = Validator::make($request->all(), [
            'id_menu' => 'required|exists:menu_makanan,id_menu',
            'id_template_item' => 'required|exists:template_items,id_template_item',
            'jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
        ], [
            'id_menu.required' => 'Menu harus dipilih',
            'id_template_item.required' => 'Template bahan harus dipilih',
            'jumlah_per_porsi.required' => 'Jumlah per porsi harus diisi',
            'jumlah_per_porsi.min' => 'Jumlah per porsi minimal 0.0001',
        ]);

        $exists = BahanMenu::where('id_menu', $request->id_menu)
            ->where('id_template_item', $request->id_template_item)
            ->where('id_bahan_menu', '!=', $bahanMenu->id_bahan_menu)
            ->exists();

        if ($exists) {
            $validator->after(function ($validator) {
                $validator->errors()->add('id_template_item', 'Bahan ini sudah ditambahkan ke menu tersebut');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bahanMenu->update([
            'id_menu' => $request->id_menu,
            'id_template_item' => $request->id_template_item,
            'jumlah_per_porsi' => $request->jumlah_per_porsi
        ]);

        return redirect()->route('bahan-menu.index-by-menu', ['menu' => $request->id_menu])
            ->with('success', 'Bahan menu berhasil diperbarui');
    }

    public function destroy(BahanMenu $bahanMenu)
    {
        $menuId = $bahanMenu->id_menu;
        $bahanMenu->delete();

        return redirect()->route('bahan-menu.index-by-menu', ['menu' => $menuId])
            ->with('success', 'Bahan menu berhasil dihapus');
    }

    public function bulkUpdate(Request $request, MenuMakanan $menu)
    {
        $validator = Validator::make($request->all(), [
            'bahan_menu' => 'required|array|min:1',
            'bahan_menu.*.id_template_item' => 'required|exists:template_items,id_template_item',
            'bahan_menu.*.jumlah_per_porsi' => 'required|numeric|min:0.0001|max:999999.9999',
        ], [
            'bahan_menu.required' => 'Minimal harus ada 1 bahan menu',
            'bahan_menu.*.id_template_item.required' => 'Template bahan harus dipilih',
            'bahan_menu.*.jumlah_per_porsi.required' => 'Jumlah per porsi harus diisi',
            'bahan_menu.*.jumlah_per_porsi.min' => 'Jumlah per porsi minimal 0.0001',
        ]);

        $templateIds = collect($request->bahan_menu)->pluck('id_template_item')->toArray();
        if (count($templateIds) !== count(array_unique($templateIds))) {
            $validator->after(function ($validator) {
                $validator->errors()->add('bahan_menu', 'Tidak boleh ada bahan yang sama dalam satu menu');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $menu->bahanMenu()->delete();

        foreach ($request->bahan_menu as $bahan) {
            $menu->bahanMenu()->create([
                'id_template_item' => $bahan['id_template_item'],
                'jumlah_per_porsi' => $bahan['jumlah_per_porsi']
            ]);
        }

        return redirect()->route('bahan-menu.index-by-menu', ['menu' => $menu->id_menu])
            ->with('success', 'Bahan menu berhasil diperbarui');
    }

    public function getByMenu(MenuMakanan $menu)
    {
        $bahanMenus = $menu->bahanMenu()
            ->with(['templateItem'])
            ->get()
            ->map(function ($bahan) {
                return [
                    'id_bahan_menu' => $bahan->id_bahan_menu,
                    'id_template_item' => $bahan->id_template_item,
                    'nama_bahan' => $bahan->templateItem->nama_bahan,
                    'satuan' => $bahan->templateItem->satuan,
                    'jumlah_per_porsi' => $bahan->jumlah_per_porsi,
                ];
            });

        return response()->json($bahanMenus);
    }

    public function calculateIngredients(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menus' => 'required|array|min:1',
            'menus.*.id_menu' => 'required|exists:menu_makanan,id_menu',
            'menus.*.porsi' => 'required|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Data tidak valid'], 400);
        }

        $totalIngredients = [];

        foreach ($request->menus as $menuData) {
            $menu = MenuMakanan::find($menuData['id_menu']);
            $ingredients = $menu->calculateRequiredIngredients($menuData['porsi']);

            foreach ($ingredients as $ingredient) {
                $templateId = $ingredient['id_template_item'];

                if (isset($totalIngredients[$templateId])) {
                    $totalIngredients[$templateId]['total_needed'] += $ingredient['total_needed'];
                } else {
                    $totalIngredients[$templateId] = $ingredient;
                }
            }
        }

        return response()->json([
            'ingredients' => array_values($totalIngredients),
            'total_items' => count($totalIngredients)
        ]);
    }
}
