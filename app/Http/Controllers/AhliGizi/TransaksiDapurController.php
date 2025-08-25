<?php

namespace App\Http\Controllers\AhliGizi;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDapur;
use App\Models\DetailTransaksiDapur;
use App\Models\MenuMakanan;
use App\Models\Dapur;
use App\Models\AhliGizi;
use App\Models\KepalaDapur;
use App\Models\ApprovalTransaksi;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TransaksiDapurController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $transaksi = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->with([
                'detailTransaksiDapur.menuMakanan',
                'approvalTransaksi',
                'laporanKekuranganStock',
                'dapur'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ahligizi.transaksi.index', compact('transaksi', 'ahliGizi'));
    }

    public function create()
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized - Anda bukan ahli gizi');
        }

        $existingDraft = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->where('status', 'draft')
            ->first();

        if ($existingDraft) {
            return redirect()->route('ahli-gizi.transaksi.edit-porsi-besar', $existingDraft)
                ->with('info', 'Anda memiliki draft Input Paket Menu yang belum selesai. Silakan lanjutkan atau hapus terlebih dahulu.');
        }

        return view('ahligizi.transaksi.create', compact('ahliGizi'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'tanggal_transaksi' => 'required|date|after_or_equal:today',
            // 'nama_paket' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:500'
        ]);


        $existingDraft = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->where('status', 'draft')
            ->exists();

        if ($existingDraft) {
            return redirect()->back()
                ->with('error', 'Anda masih memiliki draft transaksi yang belum selesai.');
        }

        $transaksi = TransaksiDapur::create([
            'id_dapur' => $ahliGizi->id_dapur,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'nama_paket' => $request->nama_paket,
            'keterangan' => $request->keterangan,
            'status' => 'draft',
            'created_by' => $user->id_user,
            'total_porsi' => 0
        ]);

        return redirect()->route('ahli-gizi.transaksi.edit-porsi-besar', $transaksi)
            ->with('success', 'Input Paket Menu berhasil dibuat. Silakan input porsi besar.');
    }

    public function editPorsiBesar(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu tidak dapat diedit karena sudah diproses.');
        }

        $porsiBesar = $transaksi->detailTransaksiDapur()
            ->where('tipe_porsi', 'besar')
            ->with('menuMakanan.bahanMenu.templateItem')
            ->get();

        $menus = MenuMakanan::active()
            ->with(['bahanMenu.templateItem'])
            ->get();

        return view('ahligizi.transaksi.edit-porsi-besar', compact('transaksi', 'porsiBesar', 'menus', 'ahliGizi'));
    }

    public function updatePorsiBesar(Request $request, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu tidak dapat diedit.');
        }

        $request->validate([
            'menus' => 'required|array|min:1',
            'menus.*.id_menu' => [
                'required',
                'exists:menu_makanan,id_menu',
                function ($attribute, $value, $fail) {
                    $menu = MenuMakanan::find($value);
                    if (!$menu || !$menu->is_active) {
                        $fail('Menu yang dipilih tidak aktif.');
                    }
                }
            ],
            'menus.*.jumlah_porsi' => 'required|integer|min:1|max:1000000'
        ], [
            'menus.required' => 'Minimal harus ada 1 menu untuk porsi besar',
            'menus.*.id_menu.required' => 'Menu harus dipilih',
            'menus.*.id_menu.exists' => 'Menu yang dipilih tidak valid',
            'menus.*.jumlah_porsi.required' => 'Jumlah porsi harus diisi',
            'menus.*.jumlah_porsi.min' => 'Jumlah porsi minimal 1',
            'menus.*.jumlah_porsi.max' => 'Jumlah porsi maksimal 1000000'
        ]);

        $menuIds = array_column($request->menus, 'id_menu');
        if (count($menuIds) !== count(array_unique($menuIds))) {
            return redirect()->back()
                ->with('error', 'Menu tidak boleh duplikat')
                ->withInput();
        }

        DB::transaction(function () use ($request, $transaksi) {
            $transaksi->detailTransaksiDapur()->where('tipe_porsi', 'besar')->delete();

            foreach ($request->menus as $menuData) {
                DetailTransaksiDapur::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $menuData['id_menu'],
                    'jumlah_porsi' => $menuData['jumlah_porsi'],
                    'tipe_porsi' => 'besar'
                ]);
            }

            $totalPorsiBesar = collect($request->menus)->sum('jumlah_porsi');
            $transaksi->update(['total_porsi_besar' => $totalPorsiBesar]);
        });

        return redirect()->route('ahli-gizi.transaksi.edit-porsi-kecil', $transaksi)
            ->with('success', 'Porsi besar berhasil disimpan. Silakan input porsi kecil.');
    }

    public function editPorsiKecil(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu tidak dapat diedit.');
        }

        $porsiBesarCount = $transaksi->detailTransaksiDapur()
            ->where('tipe_porsi', 'besar')->count();

        if ($porsiBesarCount === 0) {
            return redirect()->route('ahli-gizi.transaksi.edit-porsi-besar', $transaksi)
                ->with('error', 'Harap isi porsi besar terlebih dahulu.');
        }

        $porsiKecil = $transaksi->detailTransaksiDapur()
            ->where('tipe_porsi', 'kecil')
            ->with('menuMakanan.bahanMenu.templateItem')
            ->get();

        $menus = MenuMakanan::active()
            ->with(['bahanMenu.templateItem'])
            ->get();

        return view('ahligizi.transaksi.edit-porsi-kecil', compact('transaksi', 'porsiKecil', 'menus', 'ahliGizi'));
    }

    public function updatePorsiKecil(Request $request, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu tidak dapat diedit.');
        }

        $request->validate([
            'menus' => 'nullable|array',
            'menus.*.id_menu' => [
                'required',
                'exists:menu_makanan,id_menu',
                function ($attribute, $value, $fail) {
                    $menu = MenuMakanan::find($value);
                    if (!$menu || !$menu->is_active) {
                        $fail('Menu yang dipilih tidak aktif.');
                    }
                }
            ],
            'menus.*.jumlah_porsi' => 'required|integer|min:1|max:1000000'
        ]);

        if ($request->has('menus') && !empty($request->menus)) {
            $menuIds = array_column($request->menus, 'id_menu');
            if (count($menuIds) !== count(array_unique($menuIds))) {
                return redirect()->back()
                    ->with('error', 'Menu tidak boleh duplikat')
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $transaksi) {
            $transaksi->detailTransaksiDapur()->where('tipe_porsi', 'kecil')->delete();

            $totalPorsiKecil = 0;
            if ($request->has('menus') && !empty($request->menus)) {
                foreach ($request->menus as $menuData) {
                    DetailTransaksiDapur::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_menu' => $menuData['id_menu'],
                        'jumlah_porsi' => $menuData['jumlah_porsi'],
                        'tipe_porsi' => 'kecil'
                    ]);
                    $totalPorsiKecil += $menuData['jumlah_porsi'];
                }
            }

            $totalPorsiBesar = $transaksi->detailTransaksiDapur()
                ->where('tipe_porsi', 'besar')
                ->sum('jumlah_porsi');

            $transaksi->update([
                'total_porsi_kecil' => $totalPorsiKecil,
                'total_porsi' => $totalPorsiBesar + $totalPorsiKecil
            ]);
        });

        return redirect()->route('ahli-gizi.transaksi.preview', $transaksi)
            ->with('success', 'Porsi kecil berhasil disimpan. Silakan review Input Paket Menu.');
    }

    public function preview(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi);
        }

        $transaksi->load([
            'detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'dapur',
            'laporanKekuranganStock.templateItem'
        ]);

        $stockCheck = $transaksi->checkAllStockAvailability();

        $bahanKebutuhan = $this->calculateIngredientNeeds($transaksi);

        return view('ahligizi.transaksi.preview', compact('transaksi', 'stockCheck', 'bahanKebutuhan', 'ahliGizi'));
    }

    public function submitApproval(Request $request, TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu sudah diproses.');
        }

        if ($transaksi->detailTransaksiDapur()->where('tipe_porsi', 'besar')->count() === 0) {
            return redirect()->route('ahli-gizi.transaksi.edit-porsi-besar', $transaksi)
                ->with('error', 'Input Paket Menu harus memiliki minimal 1 porsi besar.');
        }

        $request->validate([
            'keterangan_pengajuan' => 'nullable|string|max:500'
        ]);

        $kepalaDapur = KepalaDapur::where('id_dapur', $transaksi->id_dapur)->first();
        if (!$kepalaDapur) {
            return redirect()->back()
                ->with('error', 'Kepala dapur tidak ditemukan untuk dapur ini.');
        }

        $stockCheck = $transaksi->checkAllStockAvailability();

        if (!$stockCheck['can_produce']) {
            $transaksi->createShortageReport();

            return redirect()->route('ahli-gizi.transaksi.preview', $transaksi)
                ->with('error', 'Stock tidak mencukupi. Laporan kekurangan telah dibuat dan dikirim ke Kepala Dapur.')
                ->with('shortages', $stockCheck['shortages']);
        }

        DB::beginTransaction();
        try {
            $success = $transaksi->submitForApproval(
                $ahliGizi->id_ahli_gizi,
                $kepalaDapur->id_kepala_dapur,
                $request->keterangan_pengajuan
            );

            if ($success) {
                DB::commit();
                return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                    ->with('success', 'Input Paket Menu berhasil diajukan untuk persetujuan Kepala Dapur.');
            }

            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengajukan Input Paket Menu untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi error: ' . $e->getMessage());
        }
    }

    public function createShortageReport(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.show', $transaksi)
                ->with('error', 'Input Paket Menu sudah diproses.');
        }

        $stockCheck = $transaksi->checkAllStockAvailability();

        if ($stockCheck['can_produce']) {
            return redirect()->route('ahli-gizi.transaksi.preview', $transaksi)
                ->with('info', 'Stock mencukupi. Tidak perlu laporan kekurangan.');
        }

        $success = $transaksi->createShortageReport();

        if ($success) {
            return redirect()->route('ahli-gizi.transaksi.preview', $transaksi)
                ->with('success', 'Laporan kekurangan stock berhasil dibuat dan dikirim ke Kepala Dapur.')
                ->with('shortages', $stockCheck['shortages']);
        }

        return redirect()->back()
            ->with('error', 'Gagal membuat laporan kekurangan stock.');
    }

    public function show(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        $transaksi->load([
            'detailTransaksiDapur.menuMakanan.bahanMenu.templateItem',
            'approvalTransaksi',
            'laporanKekuranganStock.templateItem',
            'dapur',
            'createdBy'
        ]);

        $bahanKebutuhan = $this->calculateIngredientNeeds($transaksi);
        $bahanBesar = $this->calculateIngredientNeedsByType($transaksi, 'besar');
        $bahanKecil = $this->calculateIngredientNeedsByType($transaksi, 'kecil');

        $stockData = [];
        $shortages = [];
        foreach ($bahanKebutuhan as $idTemplate => $bahan) {
            $stockItem = StockItem::where('id_dapur', $transaksi->id_dapur)
                ->where('id_template_item', $idTemplate)
                ->first();

            $stockTersedia = $stockItem ? (float)$stockItem->jumlah : 0.0;

            $stockData[$idTemplate] = [
                'nama_bahan' => $bahan['nama_bahan'],
                'satuan' => $bahan['satuan'],
                'kebutuhan' => $bahan['total_kebutuhan'],
                'stock_tersedia' => $stockTersedia,
                'sufficient' => $stockTersedia >= $bahan['total_kebutuhan'],
                'debug' => $stockItem ? 'found' : 'not_found',
                'satuan_stok' => $stockItem ? $stockItem->satuan : $bahan['satuan']
            ];

            if ($stockTersedia < $bahan['total_kebutuhan']) {
                $shortages[] = [
                    'id_template_item' => $idTemplate,
                    'nama_bahan' => $bahan['nama_bahan'],
                    'kebutuhan' => $bahan['total_kebutuhan'],
                    'stock_tersedia' => $stockTersedia,
                    'kekurangan' => $bahan['total_kebutuhan'] - $stockTersedia,
                    'satuan' => $bahan['satuan'],
                    'percentage_shortage' => $stockTersedia > 0
                        ? round((($bahan['total_kebutuhan'] - $stockTersedia) / $bahan['total_kebutuhan']) * 100, 2)
                        : 100
                ];
            }
        }

        return view('ahligizi.transaksi.show', compact(
            'transaksi',
            'bahanKebutuhan',
            'bahanBesar',
            'bahanKecil',
            'stockData',
            'shortages',
            'ahliGizi'
        ));
    }
    public function destroy(TransaksiDapur $transaksi)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
            abort(403, 'Unauthorized');
        }

        if ($transaksi->status !== 'draft') {
            return redirect()->route('ahli-gizi.transaksi.index')
                ->with('error', 'Hanya draft Input Paket Menu yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $transaksi->detailTransaksiDapur()->delete();

            $transaksi->laporanKekuranganStock()->delete();

            $transaksi->delete();

            DB::commit();
            return redirect()->route('ahli-gizi.transaksi.index')
                ->with('success', 'Input Paket Menu berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ahli-gizi.transaksi.index')
                ->with('error', 'Gagal menghapus Input Paket Menu: ' . $e->getMessage());
        }
    }

    public function getMenuDetail(MenuMakanan $menuMakanan)
    {
        try {
            if (!$menuMakanan->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak aktif'
                ], 400);
            }

            $menuMakanan->load(['bahanMenu.templateItem']);

            $menuData = [
                'id_menu' => $menuMakanan->id_menu,
                'nama_menu' => $menuMakanan->nama_menu,
                'gambar' => $menuMakanan->gambar_url,
                'deskripsi' => $menuMakanan->deskripsi,
                'kategori' => $menuMakanan->kategori,
                'is_active' => $menuMakanan->is_active,
                'bahan_menu' => $menuMakanan->bahanMenu->map(function ($bahan) {
                    return [
                        'id_bahan_menu' => $bahan->id_bahan_menu,
                        'id_template_item' => $bahan->id_template_item,
                        'nama_bahan' => $bahan->templateItem->nama_bahan,
                        'jumlah_per_porsi' => (float) $bahan->jumlah_per_porsi,
                        'satuan' => $bahan->templateItem->satuan,
                        'is_bahan_basah' => $bahan->is_bahan_basah,
                        'template_item' => [
                            'id_template_item' => $bahan->templateItem->id_template_item,
                            'nama_bahan' => $bahan->templateItem->nama_bahan,
                            'satuan' => $bahan->templateItem->satuan,
                            'keterangan' => $bahan->templateItem->keterangan ?? ''
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
    private function calculateIngredientNeeds(TransaksiDapur $transaksi)
    {
        $kebutuhan = [];

        foreach ($transaksi->detailTransaksiDapur as $detail) {
            foreach ($detail->menuMakanan->bahanMenu as $bahanMenu) {
                $idTemplate = $bahanMenu->id_template_item;
                $totalKebutuhan = $bahanMenu->jumlah_per_porsi * $detail->jumlah_porsi;

                if (!isset($kebutuhan[$idTemplate])) {
                    $kebutuhan[$idTemplate] = [
                        'nama_bahan' => $bahanMenu->templateItem->nama_bahan,
                        'satuan' => $bahanMenu->templateItem->satuan,
                        'total_kebutuhan' => 0,
                        'detail_penggunaan' => []
                    ];
                }

                $kebutuhan[$idTemplate]['total_kebutuhan'] += $totalKebutuhan;
                $kebutuhan[$idTemplate]['detail_penggunaan'][] = [
                    'menu' => $detail->menuMakanan->nama_menu,
                    'tipe_porsi' => $detail->tipe_porsi,
                    'jumlah_porsi' => $detail->jumlah_porsi,
                    'kebutuhan_per_porsi' => $bahanMenu->jumlah_per_porsi,
                    'total_kebutuhan' => $totalKebutuhan
                ];
            }
        }

        return $kebutuhan;
    }

    private function calculateIngredientNeedsByType(TransaksiDapur $transaksi, $tipePorsi)
    {
        $bahan = [];
        foreach ($transaksi->detailTransaksiDapur()->where('tipe_porsi', $tipePorsi)->with('menuMakanan.bahanMenu.templateItem')->get() as $detail) {
            foreach ($detail->menuMakanan->bahanMenu as $bahanMenu) {
                $idTemplate = $bahanMenu->id_template_item;
                if (!isset($bahan[$idTemplate])) {
                    $bahan[$idTemplate] = [
                        'nama_bahan' => $bahanMenu->templateItem->nama_bahan,
                        'satuan' => $bahanMenu->templateItem->satuan,
                        'total_kebutuhan' => 0
                    ];
                }
                $bahan[$idTemplate]['total_kebutuhan'] += $bahanMenu->jumlah_per_porsi * $detail->jumlah_porsi;
            }
        }
        return $bahan;
    }
    public function trackingStatus(Request $request)
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            abort(403, 'Unauthorized');
        }

        $query = TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
            ->where('created_by', $user->id_user)
            ->with([
                'detailTransaksiDapur.menuMakanan',
                'approvalTransaksi.kepalaDapur.user',
                'laporanKekuranganStock'
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(15);

        $statusSummary = [
            'draft' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'draft')
                ->count(),
            'pending_approval' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'pending_approval')
                ->count(),
            'completed' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'completed')
                ->count(),
            'rejected' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'rejected')
                ->count(),
            'with_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->whereHas('laporanKekuranganStock')
                ->count(),
        ];

        return view('ahligizi.transaksi.tracking-status', compact('transaksi', 'statusSummary', 'ahliGizi'));
    }

    public function checkStockAvailability(TransaksiDapur $transaksi)
    {
        try {
            $user = Auth::user();
            $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
                $query->where('id_user', $user->id_user);
            })->first();

            if (!$ahliGizi || $transaksi->created_by !== $user->id_user || $transaksi->id_dapur !== $ahliGizi->id_dapur) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $bahanKebutuhan = $this->calculateIngredientNeeds($transaksi);

            $stockData = [];
            $shortages = [];
            $canProduce = true;

            foreach ($bahanKebutuhan as $idTemplate => $bahan) {
                Log::debug('Checking StockItem', [
                    'id_dapur' => $transaksi->id_dapur,
                    'id_template_item' => $idTemplate,
                    'nama_bahan' => $bahan['nama_bahan']
                ]);

                $stockItem = StockItem::where('id_dapur', $transaksi->id_dapur)
                    ->where('id_template_item', $idTemplate)
                    ->first();

                if (!$stockItem) {
                    Log::warning('StockItem not found', [
                        'id_dapur' => $transaksi->id_dapur,
                        'id_template_item' => $idTemplate,
                        'nama_bahan' => $bahan['nama_bahan']
                    ]);
                    $stockTersedia = 0.0;
                    $debugStatus = 'not_found';
                } else {
                    Log::debug('StockItem found', [
                        'id_dapur' => $transaksi->id_dapur,
                        'id_template_item' => $idTemplate,
                        'nama_bahan' => $bahan['nama_bahan'],
                        'jumlah_raw' => $stockItem->jumlah,
                        'jumlah_type' => gettype($stockItem->jumlah),
                        'satuan' => $stockItem->satuan
                    ]);
                    $stockTersedia = is_numeric($stockItem->jumlah)
                        ? (float) $stockItem->jumlah
                        : 0.0;
                    $debugStatus = 'found';
                }

                $kebutuhan = $bahan['total_kebutuhan'];

                $stockData[$idTemplate] = [
                    'nama_bahan' => $bahan['nama_bahan'],
                    'satuan' => $bahan['satuan'],
                    'kebutuhan' => $kebutuhan,
                    'stock_tersedia' => $stockTersedia,
                    'sufficient' => $stockTersedia >= $kebutuhan,
                    'debug' => $debugStatus,
                    'satuan_stok' => $stockItem ? $stockItem->satuan : $bahan['satuan']
                ];

                if ($stockTersedia < $kebutuhan) {
                    $canProduce = false;
                    $shortages[] = [
                        'id_template_item' => $idTemplate,
                        'nama_bahan' => $bahan['nama_bahan'],
                        'kebutuhan' => $kebutuhan,
                        'stock_tersedia' => $stockTersedia,
                        'kekurangan' => $kebutuhan - $stockTersedia,
                        'satuan' => $bahan['satuan'],
                        'percentage_shortage' => $stockTersedia > 0
                            ? round((($kebutuhan - $stockTersedia) / $kebutuhan) * 100, 2)
                            : 100
                    ];
                }
            }

            Log::debug('Stock Check Result', [
                'transaksi_id' => $transaksi->id_transaksi,
                'stock_data' => $stockData,
                'shortages' => $shortages
            ]);

            return response()->json([
                'success' => true,
                'can_produce' => $canProduce,
                'shortages' => $shortages,
                'stock_data' => $stockData,
                'bahan_kebutuhan' => $bahanKebutuhan,
                'total_ingredients' => count($bahanKebutuhan),
                'sufficient_ingredients' => count($bahanKebutuhan) - count($shortages),
                'shortage_count' => count($shortages),
                'message' => $canProduce
                    ? 'Stock mencukupi untuk semua bahan'
                    : 'Terdapat kekurangan stock pada beberapa bahan',
                'summary' => [
                    'total_bahan' => count($bahanKebutuhan),
                    'bahan_cukup' => count($bahanKebutuhan) - count($shortages),
                    'bahan_kurang' => count($shortages),
                    'persentase_kecukupan' => count($bahanKebutuhan) > 0
                        ? round(((count($bahanKebutuhan) - count($shortages)) / count($bahanKebutuhan)) * 100, 2)
                        : 0
                ],
                'debug_id_dapur' => $transaksi->id_dapur
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkStockAvailability: ' . $e->getMessage(), [
                'transaksi_id' => $transaksi->id_transaksi,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memeriksa stock',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function getDashboardSummary()
    {
        $user = Auth::user();
        $ahliGizi = AhliGizi::whereHas('userRole', function ($query) use ($user) {
            $query->where('id_user', $user->id_user);
        })->first();

        if (!$ahliGizi) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = now();
        $thisWeek = $today->copy()->startOfWeek();
        $thisMonth = $today->copy()->startOfMonth();

        $summary = [
            'today' => [
                'total' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->whereDate('created_at', $today)
                    ->count(),
                'completed' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $today)
                    ->count(),
            ],
            'this_week' => [
                'total' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->where('created_at', '>=', $thisWeek)
                    ->count(),
                'pending' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->where('status', 'pending_approval')
                    ->where('created_at', '>=', $thisWeek)
                    ->count(),
            ],
            'this_month' => [
                'total' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->where('created_at', '>=', $thisMonth)
                    ->count(),
                'with_shortage' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                    ->where('created_by', $user->id_user)
                    ->whereHas('laporanKekuranganStock')
                    ->where('created_at', '>=', $thisMonth)
                    ->count(),
            ],
            'draft_count' => TransaksiDapur::where('id_dapur', $ahliGizi->id_dapur)
                ->where('created_by', $user->id_user)
                ->where('status', 'draft')
                ->count(),
        ];

        return response()->json($summary);
    }
}
