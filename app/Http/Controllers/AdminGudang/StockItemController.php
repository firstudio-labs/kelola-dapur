<?php

namespace App\Http\Controllers\AdminGudang;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\TemplateItem;
use App\Models\ApprovalStockItem;
use App\Models\ApprovalStockItemDokumentasi;
use App\Models\Dapur;
use App\Models\TransaksiDapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class StockItemController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $user     = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $this->ensureStockItemsExist($dapur);

        $query = StockItem::with([
            'templateItem',
            'dapur',
            'latestApprovedRequest' => function ($query) {
                $query->where('status', 'approved')
                    ->orderBy('approved_at', 'desc')
                    ->limit(1);
            }
        ])->where('id_dapur', $dapur->id_dapur);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('templateItem', function ($q) use ($searchTerm) {
                $q->where('nama_bahan', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'habis':
                    $query->where('jumlah', 0);
                    break;
                case 'rendah':
                    $query->where('jumlah', '>', 0)->where('jumlah', '<=', 10);
                    break;
                case 'normal':
                    $query->where('jumlah', '>', 10);
                    break;
            }
        }

        if ($request->filled('satuan')) {
            $query->whereHas('templateItem', function ($q) use ($request) {
                $q->where('satuan', $request->satuan);
            });
        }

        $sortBy    = $request->get('sort', 'nama_bahan');
        $sortOrder = $request->get('order', 'asc');

        if ($sortBy === 'nama_bahan') {
            $query->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
                ->orderBy('template_items.nama_bahan', $sortOrder)
                ->select('stock_items.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $stockItems  = $query->paginate(15)->appends($request->query());

        $totalItems  = StockItem::where('id_dapur', $dapur->id_dapur)->count();
        $habisStok   = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', 0)->count();
        $rendahStok  = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 0)->where('jumlah', '<=', 10)->count();
        $normalStok  = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 10)->count();

        $availableSatuans = TemplateItem::whereHas('stockItems', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->distinct()
            ->pluck('satuan')
            ->filter()
            ->sort();

        $suppliers = \App\Models\Supplier::where('id_dapur', $dapur->id_dapur)->orderBy('nama_supplier', 'asc')->get();

        return view('admingudang.stock.index', compact(
            'stockItems',
            'dapur',
            'totalItems',
            'habisStok',
            'rendahStok',
            'normalStok',
            'availableSatuans',
            'suppliers'
        ));
    }

    public function show(Dapur $dapur, StockItem $stockItem)
    {
        $user     = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $stockItem->load(['templateItem', 'dapur']);

        $approvalHistory = ApprovalStockItem::with(['adminGudang.user', 'kepalaDapur.user', 'suppliers.supplier', 'suppliers.dokumentasi', 'dokumentasi'])
            ->where('id_stock_item', $stockItem->id_stock_item)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalRequests    = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->count();
        $approvedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->where('status', 'approved')->count();
        $rejectedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->where('status', 'rejected')->count();
        $pendingRequests  = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->where('status', 'pending')->count();

        $roleType         = 'admin_gudang';
        $routePrefix      = 'admin-gudang';
        $layoutTemplate   = 'template_admin_gudang.layout';
        $stockIndexLabel  = 'Kelola Stok';

        $suppliers = \App\Models\Supplier::where('id_dapur', $dapur->id_dapur)->orderBy('nama_supplier', 'asc')->get();

        return view('admingudang.stock.show', compact(
            'stockItem',
            'dapur',
            'approvalHistory',
            'totalRequests',
            'approvedRequests',
            'rejectedRequests',
            'pendingRequests',
            'roleType',
            'routePrefix',
            'layoutTemplate',
            'stockIndexLabel',
            'suppliers'
        ));
    }

    public function requestStock(Request $request, Dapur $dapur, StockItem $stockItem)
    {
        $user     = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $inputs = $request->all();
        if (isset($inputs['jumlah'])) {
            $inputs['jumlah'] = parseIndonesianNumber($inputs['jumlah']);
        }
        if (isset($inputs['suppliers']) && is_array($inputs['suppliers'])) {
            foreach ($inputs['suppliers'] as $idx => $sup) {
                if (isset($sup['jumlah'])) {
                    $inputs['suppliers'][$idx]['jumlah'] = parseIndonesianNumber($sup['jumlah']);
                }
            }
        }
        $request->merge($inputs);

        $request->validate([
            'jumlah'              => 'required|numeric|min:0.001|max:2000000000',
            'input_mode'          => 'required|in:asli,konversi',
            'keterangan'          => 'nullable|string|max:500',
            'jam_kedatangan'      => 'nullable|date_format:H:i',
            'tanggal_produksi'    => 'nullable|date',
            'tanggal_expired'     => 'nullable|date|after_or_equal:tanggal_produksi',
            'suhu_bahan_makanan'  => 'nullable|numeric|min:-50|max:100',
            'warna_bahan_makanan' => 'nullable|string|max:50',
            'foto_bahan'          => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'suppliers'           => 'nullable|array',
            'suppliers.*.id_supplier' => 'required_with:suppliers|exists:suppliers,id_supplier',
            'suppliers.*.jumlah'  => 'required_with:suppliers|numeric|min:0.001',
            'suppliers.*.fotos'   => 'nullable|array',
            'suppliers.*.fotos.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'jumlah.required'              => 'Jumlah harus diisi',
            'jumlah.numeric'               => 'Jumlah harus berupa angka',
            'jumlah.min'                   => 'Jumlah minimal 0.1',
            'jumlah.max'                   => 'Jumlah maksimal 2000000000',
            'keterangan.max'               => 'Keterangan maksimal 500 karakter',
            'jam_kedatangan.date_format'   => 'Format jam kedatangan tidak valid (HH:MM)',
            'tanggal_produksi.date'        => 'Tanggal produksi tidak valid',
            'tanggal_expired.date'         => 'Tanggal expired tidak valid',
            'tanggal_expired.after_or_equal' => 'Tanggal expired harus sama atau setelah tanggal produksi',
            'suhu_bahan_makanan.numeric'   => 'Suhu harus berupa angka',
            'suhu_bahan_makanan.min'       => 'Suhu minimal -50°C',
            'suhu_bahan_makanan.max'       => 'Suhu maksimal 100°C',
            'warna_bahan_makanan.max'      => 'Warna maksimal 50 karakter',
            'foto_bahan.image'             => 'File harus berupa gambar',
            'foto_bahan.mimes'             => 'Format gambar harus jpeg, jpg, atau png',
            'foto_bahan.max'               => 'Ukuran gambar maksimal 5MB',
        ]);

        try {
            DB::beginTransaction();

            $adminGudang = \App\Models\AdminGudang::where('id_user_role', $user->userRole->id_user_role)->first();

            if (!$adminGudang) {
                throw new \Exception('Admin gudang tidak ditemukan untuk user ini.');
            }

            $kepalaDapur = $dapur->kepalaDapur()->first();

            if (!$kepalaDapur) {
                throw new \Exception('Tidak ada kepala dapur yang ditemukan untuk dapur ini.');
            }

            $fotoPath = null;
            if ($request->hasFile('foto_bahan')) {
                $fotoPath = $this->processAndStoreImage($request->file('foto_bahan'), $dapur->id_dapur, $stockItem->id_stock_item);
            }

            $masterJumlah = (float) $request->jumlah;
            $finalSuppliers = $request->suppliers ?? [];
            
            if ($request->input_mode === 'konversi' && $stockItem->konversi_nilai > 0) {
                $masterJumlah = $masterJumlah * (float) $stockItem->konversi_nilai;
                foreach ($finalSuppliers as $idx => $sup) {
                    $finalSuppliers[$idx]['jumlah'] = ((float) $sup['jumlah']) * (float) $stockItem->konversi_nilai;
                }
            }

            $totalSupplierJumlah = 0;
            if (!empty($finalSuppliers)) {
                foreach ($finalSuppliers as $sup) {
                    $totalSupplierJumlah += (float) $sup['jumlah'];
                }
                if (round($totalSupplierJumlah, 3) > round($masterJumlah, 3)) {
                    throw new \Exception('Total jumlah dari rincian supplier (' . round($totalSupplierJumlah, 3) . ') melebihi total keseluruhan penambahan (' . round($masterJumlah, 3) . ').');
                }
            }

            $baseApprovalData = [
                'id_admin_gudang'     => $adminGudang->id_admin_gudang,
                'id_kepala_dapur'     => $kepalaDapur->id_kepala_dapur,
                'id_stock_item'       => $stockItem->id_stock_item,
                'satuan'              => $stockItem->templateItem->satuan,
                'status'              => 'approved',
                'keterangan'          => $request->keterangan,
                'jam_kedatangan'      => $request->jam_kedatangan ? $request->jam_kedatangan . ':00' : null,
                'tanggal_produksi'    => $request->tanggal_produksi,
                'tanggal_expired'     => $request->tanggal_expired,
                'suhu_bahan_makanan'  => $request->suhu_bahan_makanan,
                'warna_bahan_makanan' => $request->warna_bahan_makanan,
                'foto_bahan'          => $fotoPath,
                'approved_at'         => now(),
            ];

            $createdApproval = ApprovalStockItem::create(array_merge($baseApprovalData, [
                'jumlah' => $masterJumlah,
                'id_supplier' => null
            ]));

            if (!empty($finalSuppliers)) {
                foreach ($finalSuppliers as $idx => $sup) {
                    $supplierItem = \App\Models\ApprovalStockItemSupplier::create([
                        'id_approval_stock_item' => $createdApproval->id_approval_stock_item,
                        'id_supplier' => $sup['id_supplier'],
                        'jumlah' => $sup['jumlah'],
                    ]);

                    if (isset($request->file('suppliers')[$idx]['fotos'])) {
                        $fotos = $request->file('suppliers')[$idx]['fotos'];
                        if (is_array($fotos)) {
                            $manager = new ImageManager(new Driver());
                            foreach ($fotos as $foto) {
                                if ($foto && $foto->isValid()) {
                                    $filename = time() . '_' . Str::random(10) . '.webp';
                                    $image = $manager->read($foto->getRealPath());
                                    if ($image->width() > 1200) {
                                        $image->scaleDown(width: 1200);
                                    }
                                    $path = 'dokumen_stock/' . $dapur->id_dapur . '/' . $stockItem->id_stock_item . '/supplier_fotos';
                                    Storage::put('public/' . $path . '/' . $filename, (string) $image->toWebp(80));
                                    
                                    ApprovalStockItemDokumentasi::create([
                                        'id_approval_stock_item' => $createdApproval->id_approval_stock_item,
                                        'id_approval_stock_item_supplier' => $supplierItem->id,
                                        'foto_path' => $path . '/' . $filename,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $currentStock = (float) $stockItem->jumlah;
            StockItem::where('id_stock_item', $stockItem->id_stock_item)
                ->where('id_dapur', $dapur->id_dapur)
                ->update([
                    'jumlah'          => $currentStock + $masterJumlah,
                    'tanggal_restok'  => now(),
                ]);

            $stockItem->refresh();

            DB::commit();

            $this->autoResolveKekuranganStock(
                $dapur->id_dapur,
                $stockItem->id_template_item,
                $request->keterangan
            );

            $this->checkAndActivatePendingTransactions(
                $dapur->id_dapur,
                $stockItem->id_template_item,
                $request->keterangan
            );

            return back()->with('success', 'Permintaan penambahan stok berhasil disetujui dan stok telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan permintaan stok: ' . $e->getMessage());
        }
    }

    protected function checkAndActivatePendingTransactions(int $idDapur, ?int $idTemplateItem = null, ?string $keterangan = null): void
    {
        $resolvePendingLaporan = function ($transaksi) use ($idTemplateItem, $keterangan) {
            $pendingQuery = $transaksi->laporanKekuranganStock()->where('status', 'pending');

            if ($idTemplateItem && $keterangan !== null && trim($keterangan) !== '') {
                (clone $pendingQuery)->where('id_template_item', $idTemplateItem)->update([
                    'status' => 'resolved',
                    'keterangan_resolve' => trim($keterangan),
                ]);
                (clone $pendingQuery)->where('id_template_item', '!=', $idTemplateItem)->update(['status' => 'resolved']);
            } else {
                $pendingQuery->update(['status' => 'resolved']);
            }
        };

        $pendingTransactions = TransaksiDapur::where('id_dapur', $idDapur)
            ->where('status', 'completed')
            ->whereDoesntHave('orderProduksi')
            ->with(['detailTransaksiDapur.menuMakanan.bahanMenu.templateItem'])
            ->get();

        foreach ($pendingTransactions as $transaksi) {
            $stockCheck = $transaksi->checkStockWithReservations();
            if ($stockCheck['can_produce']) {
                $resolvePendingLaporan($transaksi);
                $transaksi->sendToProduksi();
                Log::info('Pending transaction activated after restock', ['id_transaksi' => $transaksi->id_transaksi]);
            }
        }

        $stokKurangOrders = \App\Models\OrderProduksi::where('id_dapur', $idDapur)
            ->where('status', \App\Models\OrderProduksi::STATUS_STOK_KURANG)
            ->with(['transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem'])
            ->get();

        foreach ($stokKurangOrders as $order) {
            $transaksi  = $order->transaksiDapur;
            $stockCheck = $transaksi->checkStockWithReservations();
            if ($stockCheck['can_produce']) {
                $resolvePendingLaporan($transaksi);
                $order->status = \App\Models\OrderProduksi::STATUS_BELUM_DIBUAT;
                $order->save();
                Log::info('Stok kurang order upgraded to belum_dibuat after restock', ['id_order' => $order->id_order, 'id_transaksi' => $transaksi->id_transaksi]);
            }
        }
    }

    private function ensureStockItemsExist(Dapur $dapur): void
    {
        $templateItems = TemplateItem::all();

        foreach ($templateItems as $templateItem) {
            $existingStock = StockItem::where('id_dapur', $dapur->id_dapur)
                ->where('id_template_item', $templateItem->id_template_item)
                ->first();

            if (!$existingStock) {
                StockItem::create([
                    'id_dapur'         => $dapur->id_dapur,
                    'id_template_item' => $templateItem->id_template_item,
                    'jumlah'           => 0,
                    'satuan'           => $templateItem->satuan,
                    'tanggal_restok'   => now(),
                    'keterangan'       => 'Auto-generated stock item',
                ]);
            } else {
                if ($existingStock->satuan !== $templateItem->satuan) {
                    $existingStock->update(['satuan' => $templateItem->satuan]);
                }
            }
        }
    }

    protected function autoResolveKekuranganStock(int $idDapur, int $idTemplateItem, ?string $keterangan = null): void
    {
        $stockItem = StockItem::where('id_dapur', $idDapur)->where('id_template_item', $idTemplateItem)->first();
        if (!$stockItem) return;

        $availableStock = (float) $stockItem->jumlah;

        $reservedStockAmount = 0;
        $allReserved = TransaksiDapur::calculateReservedStock($idDapur);
        
        if (isset($allReserved[$idTemplateItem])) {
            $reservedStockAmount = (float) $allReserved[$idTemplateItem]['reserved'];
        }

        $usableStock = $availableStock - $reservedStockAmount;

        if ($usableStock <= 0) return;

        $pendingShortages = \App\Models\LaporanKekuranganStock::where('id_template_item', $idTemplateItem)
            ->where('status', 'pending')
            ->whereHas('transaksiDapur', function ($q) use ($idDapur) {
                $q->where('id_dapur', $idDapur);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($pendingShortages as $shortage) {
            $neededForTx = (float) $shortage->jumlah_kurang;

            if ($usableStock >= $neededForTx) {
                $updateData = ['status' => 'resolved'];
                if ($keterangan !== null && trim($keterangan) !== '') {
                    $updateData['keterangan_resolve'] = trim($keterangan);
                }
                $shortage->update($updateData);

                $isActiveProduction = false;
                if ($shortage->id_handler) {
                    $handlerBahan = \App\Models\ProduksiHandlerBahan::find($shortage->id_handler);
                    if ($handlerBahan && $handlerBahan->orderProduksi) {
                        if (in_array($handlerBahan->orderProduksi->status, ['sedang_dibuat', 'selesai'])) {
                            $isActiveProduction = true;
                        }
                    }
                }

                if ($isActiveProduction) {
                    $stockItem->reduceStock($neededForTx);
                }

                $usableStock -= $neededForTx;
            }
        }
    }

    public function export(Dapur $dapur)
    {
        $user     = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        $stockItems = StockItem::with(['templateItem'])
            ->where('id_dapur', $dapur->id_dapur)
            ->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
            ->orderBy('template_items.nama_bahan')
            ->select('stock_items.*')
            ->get();

        $filename = 'stock_' . $dapur->nama_dapur . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($stockItems) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Bahan', 'Jumlah', 'Satuan', 'Status', 'Tanggal Restok Terakhir', 'Keterangan']);

            foreach ($stockItems as $item) {
                fputcsv($file, [
                    $item->templateItem->nama_bahan,
                    $item->jumlah,
                    $item->templateItem->satuan,
                    $item->getStockStatus(),
                    $item->tanggal_restok ? $item->tanggal_restok->format('d/m/Y') : '-',
                    $item->keterangan ?: '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function processAndStoreImage($file, $dapurId, $stockItemId)
    {
        try {
            $directory = "stock_items/{$dapurId}/{$stockItemId}";
            $filename  = time() . '_' . uniqid() . '.webp';

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image   = $manager->read($file->getRealPath());

            if ($image->width() > 1920) {
                $image->scaleDown(width: 1920);
            }

            Storage::put('public/' . $directory . '/' . $filename, (string) $image->toWebp(85));

            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    public function updateKonversi(Request $request, Dapur $dapur, StockItem $stockItem)
    {
        $user     = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $request->validate([
            'konversi_nilai'  => 'nullable|numeric|min:0.01',
            'konversi_satuan' => 'nullable|string|max:50',
        ]);

        $stockItem->update([
            'konversi_nilai'  => $request->konversi_nilai,
            'konversi_satuan' => $request->konversi_satuan,
        ]);

        return redirect()->back()->with('success', 'Konversi stok berhasil diperbarui.');
    }
}
