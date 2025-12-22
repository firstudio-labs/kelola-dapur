<?php

namespace App\Http\Controllers\AdminGudang;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\TemplateItem;
use App\Models\ApprovalStockItem;
use App\Models\Dapur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StockItemController extends Controller
{
    public function index(Request $request, Dapur $dapur)
    {
        $user = Auth::user();
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

        $sortBy = $request->get('sort', 'nama_bahan');
        $sortOrder = $request->get('order', 'asc');

        if ($sortBy === 'nama_bahan') {
            $query->join('template_items', 'stock_items.id_template_item', '=', 'template_items.id_template_item')
                ->orderBy('template_items.nama_bahan', $sortOrder)
                ->select('stock_items.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $stockItems = $query->paginate(15)->appends($request->query());

        $totalItems = StockItem::where('id_dapur', $dapur->id_dapur)->count();
        $habisStok = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', 0)->count();
        $rendahStok = StockItem::where('id_dapur', $dapur->id_dapur)
            ->where('jumlah', '>', 0)->where('jumlah', '<=', 10)->count();
        $normalStok = StockItem::where('id_dapur', $dapur->id_dapur)->where('jumlah', '>', 10)->count();

        $availableSatuans = TemplateItem::whereHas('stockItems', function ($query) use ($dapur) {
            $query->where('id_dapur', $dapur->id_dapur);
        })
            ->distinct()
            ->pluck('satuan')
            ->filter()
            ->sort();

        return view('admingudang.stock.index', compact(
            'stockItems',
            'dapur',
            'totalItems',
            'habisStok',
            'rendahStok',
            'normalStok',
            'availableSatuans'
        ));
    }

    public function show(Dapur $dapur, StockItem $stockItem)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $stockItem->load(['templateItem', 'dapur']);

        $approvalHistory = ApprovalStockItem::with(['adminGudang.user', 'kepalaDapur.user'])
            ->where('id_stock_item', $stockItem->id_stock_item)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)->count();
        $approvedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'approved')->count();
        $rejectedRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'rejected')->count();
        $pendingRequests = ApprovalStockItem::where('id_stock_item', $stockItem->id_stock_item)
            ->where('status', 'pending')->count();

        $roleType = 'admin_gudang';
        $routePrefix = 'admin-gudang';
        $layoutTemplate = 'template_admin_gudang.layout';
        $stockIndexLabel = 'Kelola Stok';

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
            'stockIndexLabel'
        ));
    }

    public function requestStock(Request $request, Dapur $dapur, StockItem $stockItem)
    {
        $user = Auth::user();
        $userRole = $user->userRole;

        if (!$userRole || $userRole->role_type !== 'admin_gudang' || $userRole->id_dapur !== $dapur->id_dapur) {
            abort(403, 'Unauthorized access to this kitchen.');
        }

        if ($stockItem->id_dapur !== $dapur->id_dapur) {
            abort(404, 'Stock item not found for this kitchen.');
        }

        $request->validate([
            'jumlah' => 'required|numeric|min:0.1|max:2000000000',
            'keterangan' => 'nullable|string|max:500',
            'jam_kedatangan' => 'nullable|date_format:H:i',
            'tanggal_produksi' => 'nullable|date',
            'tanggal_expired' => 'nullable|date|after_or_equal:tanggal_produksi',
            'suhu_bahan_makanan' => 'nullable|numeric|min:-50|max:100',
            'warna_bahan_makanan' => 'nullable|string|max:50',
            'foto_bahan' => 'nullable|image|mimes:jpeg,jpg,png|max:5120'
        ], [
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0.1',
            'jumlah.max' => 'Jumlah maksimal 2000000000',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'jam_kedatangan.date_format' => 'Format jam kedatangan tidak valid (HH:MM)',
            'tanggal_produksi.date' => 'Tanggal produksi tidak valid',
            'tanggal_expired.date' => 'Tanggal expired tidak valid',
            'tanggal_expired.after_or_equal' => 'Tanggal expired harus sama atau setelah tanggal produksi',
            'suhu_bahan_makanan.numeric' => 'Suhu harus berupa angka',
            'suhu_bahan_makanan.min' => 'Suhu minimal -50°C',
            'suhu_bahan_makanan.max' => 'Suhu maksimal 100°C',
            'warna_bahan_makanan.max' => 'Warna maksimal 50 karakter',
            'foto_bahan.image' => 'File harus berupa gambar',
            'foto_bahan.mimes' => 'Format gambar harus jpeg, jpg, atau png',
            'foto_bahan.max' => 'Ukuran gambar maksimal 5MB'
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

            // Handle foto upload dan compress ke webp
            $fotoPath = null;
            if ($request->hasFile('foto_bahan')) {
                $fotoPath = $this->processAndStoreImage($request->file('foto_bahan'), $dapur->id_dapur, $stockItem->id_stock_item);
            }

            // Buat approval dengan status approved (auto approve)
            $approval = ApprovalStockItem::create([
                'id_admin_gudang' => $adminGudang->id_admin_gudang,
                'id_kepala_dapur' => $kepalaDapur->id_kepala_dapur,
                'id_stock_item' => $stockItem->id_stock_item,
                'jumlah' => $request->jumlah,
                'satuan' => $stockItem->templateItem->satuan,
                'status' => 'approved', // Auto approve
                'keterangan' => $request->keterangan,
                'jam_kedatangan' => $request->jam_kedatangan ? $request->jam_kedatangan . ':00' : null,
                'tanggal_produksi' => $request->tanggal_produksi,
                'tanggal_expired' => $request->tanggal_expired,
                'suhu_bahan_makanan' => $request->suhu_bahan_makanan,
                'warna_bahan_makanan' => $request->warna_bahan_makanan,
                'foto_bahan' => $fotoPath,
                'approved_at' => now() // Set approved_at langsung
            ]);

            // Update stock langsung karena auto approve
            // Pastikan hanya stock item yang spesifik yang diupdate dengan menggunakan where clause
            $currentStock = (float) $stockItem->jumlah;
            StockItem::where('id_stock_item', $stockItem->id_stock_item)
                ->where('id_dapur', $dapur->id_dapur)
                ->update([
                    'jumlah' => $currentStock + $request->jumlah,
                    'tanggal_restok' => now()
                ]);
            
            // Refresh model untuk mendapatkan data terbaru
            $stockItem->refresh();

            DB::commit();

            return redirect()->route('admin-gudang.stock.show', [$dapur, $stockItem])
                ->with('success', 'Permintaan penambahan stok berhasil disetujui dan stok telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan permintaan stok: ' . $e->getMessage());
        }
    }

    private function ensureStockItemsExist(Dapur $dapur)
    {
        $templateItems = TemplateItem::all();

        foreach ($templateItems as $templateItem) {
            $existingStock = StockItem::where('id_dapur', $dapur->id_dapur)
                ->where('id_template_item', $templateItem->id_template_item)
                ->first();

            if (!$existingStock) {
                StockItem::create([
                    'id_dapur' => $dapur->id_dapur,
                    'id_template_item' => $templateItem->id_template_item,
                    'jumlah' => 0,
                    'satuan' => $templateItem->satuan,
                    'tanggal_restok' => now(),
                    'keterangan' => 'Auto-generated stock item'
                ]);
            } else {
                if ($existingStock->satuan !== $templateItem->satuan) {
                    $existingStock->update(['satuan' => $templateItem->satuan]);
                }
            }
        }
    }

    public function export(Dapur $dapur)
    {
        $user = Auth::user();
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
            'Content-Type' => 'text/csv',
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
                    $item->keterangan ?: '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process and store image, convert to WebP format
     */
    private function processAndStoreImage($file, $dapurId, $stockItemId)
    {
        try {
            // Create directory path
            $directory = "stock_items/{$dapurId}/{$stockItemId}";
            $filename = time() . '_' . uniqid() . '.webp';

            // Get image resource based on mime type
            $mimeType = $file->getMimeType();
            $image = null;

            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getRealPath());
                    break;
                default:
                    throw new \Exception('Format gambar tidak didukung');
            }

            if (!$image) {
                throw new \Exception('Gagal memproses gambar');
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions (max 1920px width, maintain aspect ratio)
            $maxWidth = 1920;
            $newWidth = $width;
            $newHeight = $height;

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) ($height * ($maxWidth / $width));
            }

            // Create resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            if ($mimeType === 'image/png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled(
                $resizedImage,
                $image,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            // Save as WebP (quality 85 for good balance)
            $fullPath = storage_path('app/public/' . $directory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            $webpPath = $fullPath . '/' . $filename;
            imagewebp($resizedImage, $webpPath, 85);

            // Clean up
            imagedestroy($image);
            imagedestroy($resizedImage);

            // Return relative path for database storage
            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }
}
