<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class TransaksiDapur extends Model
{
    use HasFactory;

    protected $table = 'transaksi_dapur';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_dapur',
        'tanggal_transaksi',
        'keterangan',
        'status',
        'total_porsi',
        'created_by'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'total_porsi' => 'decimal:0',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detailTransaksiDapur(): HasMany
    {
        return $this->hasMany(DetailTransaksiDapur::class, 'id_transaksi');
    }

    public function approvalTransaksi()
    {
        return $this->hasOne(ApprovalTransaksi::class, 'id_transaksi');
    }

    public function laporanKekuranganStock(): HasMany
    {
        return $this->hasMany(LaporanKekuranganStock::class, 'id_transaksi');
    }

    public function orderProduksi()
    {
        return $this->hasOne(OrderProduksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function sendToProduksi(string $status = 'belum_dibuat'): bool
    {
        if ($this->orderProduksi()->exists()) {
            return true;
        }

        try {
            OrderProduksi::create([
                'id_transaksi' => $this->id_transaksi,
                'id_dapur'     => $this->id_dapur,
                'status'       => $status,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send transaction to produksi', [
                'id_transaksi' => $this->id_transaksi,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getPorsiBesar()
    {
        return $this->detailTransaksiDapur()->where('tipe_porsi', 'besar')->get();
    }

    public function getPorsiKecil()
    {
        return $this->detailTransaksiDapur()->where('tipe_porsi', 'kecil')->get();
    }

    public function getTotalPorsiBesar(): int
    {
        return (int) ($this->detailTransaksiDapur()->where('tipe_porsi', 'besar')->value('jumlah_porsi') ?? 0);
    }

    public function getTotalPorsiKecil(): int
    {
        return (int) ($this->detailTransaksiDapur()->where('tipe_porsi', 'kecil')->value('jumlah_porsi') ?? 0);
    }

    public function calculateIngredientNeeds()
    {
        $kebutuhan = [];

        foreach ($this->detailTransaksiDapur as $detail) {
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

    public function calculateIngredientNeedsByType($tipePorsi)
    {
        $bahan = [];
        foreach ($this->detailTransaksiDapur()->where('tipe_porsi', $tipePorsi)->with('menuMakanan.bahanMenu.templateItem')->get() as $detail) {
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

    public function getRequiredIngredients(): array
    {
        $allIngredients = [];

        foreach ($this->detailTransaksiDapur as $detail) {
            $requiredIngredients = $detail->menuMakanan->calculateRequiredIngredients($detail->jumlah_porsi);

            foreach ($requiredIngredients as $ingredient) {
                $key = $ingredient['id_template_item'];

                if (!isset($allIngredients[$key])) {
                    $allIngredients[$key] = [
                        'id_template_item' => $ingredient['id_template_item'],
                        'nama_bahan'       => $ingredient['nama_bahan'],
                        'satuan'           => $ingredient['satuan'],
                        'is_bahan_basah'   => $ingredient['is_bahan_basah'] ?? false,
                        'needed'           => 0,
                    ];
                }

                $neededToAdd = ($ingredient['is_bahan_basah'] ?? false)
                    ? $ingredient['total_berat_basah']
                    : $ingredient['total_needed'];

                $allIngredients[$key]['needed'] += $neededToAdd;
            }
        }

        return $allIngredients;
    }

    public static function calculateReservedStock(int $idDapur, ?int $excludeTransaksiId = null): array
    {
        $reserved = [];

        $pendingOrders = OrderProduksi::where('id_dapur', $idDapur)
            ->whereIn('status', [OrderProduksi::STATUS_BELUM_DIBUAT])
            ->with(['transaksiDapur.detailTransaksiDapur.menuMakanan.bahanMenu.templateItem'])
            ->get();

        foreach ($pendingOrders as $order) {
            $transaksi = $order->transaksiDapur;

            if ($excludeTransaksiId && $transaksi->id_transaksi === $excludeTransaksiId) {
                continue;
            }

            $ingredients = $transaksi->getRequiredIngredients();

            foreach ($ingredients as $key => $ingredient) {
                if (!isset($reserved[$key])) {
                    $reserved[$key] = [
                        'id_template_item' => $ingredient['id_template_item'],
                        'nama_bahan'       => $ingredient['nama_bahan'],
                        'satuan'           => $ingredient['satuan'],
                        'reserved'         => 0,
                    ];
                }
                $reserved[$key]['reserved'] += $ingredient['needed'];
            }
        }

        return $reserved;
    }

    public function checkAllStockAvailability(): array
    {
        $result = [
            'can_produce'         => true,
            'shortages'           => [],
            'ingredients_summary' => [],
        ];

        $allIngredients = $this->getRequiredIngredients();

        foreach ($allIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $available = $stockItem ? (float) $stockItem->jumlah : 0;
            $needed    = $ingredient['needed'];

            $ingredientData = [
                'id_template_item' => $ingredient['id_template_item'],
                'nama_bahan'       => $ingredient['nama_bahan'],
                'satuan'           => $ingredient['satuan'],
                'is_bahan_basah'   => $ingredient['is_bahan_basah'],
                'needed'           => $needed,
                'available'        => $available,
                'sufficient'       => $available >= $needed,
            ];

            if ($available < $needed) {
                $result['can_produce'] = false;
                $result['shortages'][] = [
                    'id_template_item' => $ingredient['id_template_item'],
                    'nama_bahan'       => $ingredient['nama_bahan'],
                    'satuan'           => $ingredient['satuan'],
                    'needed'           => $needed,
                    'available'        => $available,
                    'shortage'         => $needed - $available,
                ];
            }

            $result['ingredients_summary'][] = $ingredientData;
        }

        return $result;
    }

    public function checkStockWithReservations(): array
    {
        $result = [
            'can_produce'         => true,
            'shortages'           => [],
            'ingredients_summary' => [],
        ];

        $allIngredients = $this->getRequiredIngredients();
        $reservedStock  = self::calculateReservedStock($this->id_dapur, $this->id_transaksi);

        foreach ($allIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $actualStock  = $stockItem ? (float) $stockItem->jumlah : 0;
            $reserved     = $reservedStock[$ingredient['id_template_item']]['reserved'] ?? 0;
            $effectiveAvailable = max(0, $actualStock - $reserved);
            $needed       = $ingredient['needed'];

            $ingredientData = [
                'id_template_item'    => $ingredient['id_template_item'],
                'nama_bahan'          => $ingredient['nama_bahan'],
                'satuan'              => $ingredient['satuan'],
                'is_bahan_basah'      => $ingredient['is_bahan_basah'],
                'needed'              => $needed,
                'available'           => $actualStock,
                'reserved'            => $reserved,
                'effective_available' => $effectiveAvailable,
                'sufficient'          => $effectiveAvailable >= $needed,
            ];

            if ($effectiveAvailable < $needed) {
                $result['can_produce'] = false;
                $result['shortages'][] = [
                    'id_template_item' => $ingredient['id_template_item'],
                    'nama_bahan'       => $ingredient['nama_bahan'],
                    'satuan'           => $ingredient['satuan'],
                    'needed'           => $needed,
                    'available'        => $effectiveAvailable,
                    'shortage'         => $needed - $effectiveAvailable,
                    'kebutuhan'        => $needed,
                    'stock_tersedia'   => $effectiveAvailable,
                    'kekurangan'       => $needed - $effectiveAvailable,
                ];
            }

            $result['ingredients_summary'][] = $ingredientData;
        }

        return $result;
    }

    public function checkStockWithSnapshots(ApprovalTransaksi $approval = null): array
    {
        $stockCheck = $this->checkAllStockAvailability();

        if (!$approval) {
            $approval = $this->approvalTransaksi;
        }

        if (!$approval) {
            return $stockCheck;
        }

        $snapshots = StockSnapshot::where('id_approval_transaksi', $approval->id_approval_transaksi)
            ->with('templateItem')
            ->get()
            ->keyBy('id_template_item');

        $hasSnapshots = $snapshots->count() > 0;
        $stockCheck['has_snapshots'] = $hasSnapshots;

        if ($hasSnapshots) {
            foreach ($stockCheck['ingredients_summary'] as &$ingredient) {
                $snapshot = $snapshots->get($ingredient['id_template_item']);
                if ($snapshot) {
                    $ingredient['current_available'] = $ingredient['available'];
                    $ingredient['available']         = (float) $snapshot->available;
                    $ingredient['sufficient']        = $ingredient['available'] >= $ingredient['needed'];
                    $ingredient['from_snapshot']     = true;
                } else {
                    $ingredient['from_snapshot'] = false;
                }
            }

            $stockCheck['can_produce'] = collect($stockCheck['ingredients_summary'])->every(fn($i) => $i['sufficient']);

            $stockCheck['shortages'] = collect($stockCheck['ingredients_summary'])
                ->filter(fn($i) => !$i['sufficient'])
                ->map(fn($i) => [
                    'id_template_item' => $i['id_template_item'],
                    'nama_bahan'       => $i['nama_bahan'],
                    'satuan'           => $i['satuan'],
                    'needed'           => $i['needed'],
                    'available'        => $i['available'],
                    'shortage'         => $i['needed'] - $i['available'],
                ])
                ->values()
                ->toArray();
        }

        return $stockCheck;
    }

    public function createShortageReport(bool $autoResolve = false): bool
    {
        $stockCheck = $this->checkAllStockAvailability();

        if ($stockCheck['can_produce']) {
            return false;
        }

        $this->laporanKekuranganStock()->delete();

        $status = $autoResolve ? 'resolved' : 'pending';
        foreach ($stockCheck['shortages'] as $shortage) {
            LaporanKekuranganStock::createFromShortage($this->id_transaksi, $shortage, $status);
        }

        return true;
    }

    public function submitForApproval(int $ahliGiziId, int $kepalaDapurId, string $keterangan = null, bool $autoApprove = false): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $stockCheck = $this->checkAllStockAvailability();

        if (!$stockCheck['can_produce']) {
            $this->createShortageReport();
            return false;
        }

        $approvalStatus = $autoApprove ? 'approved' : 'pending';

        $approval = ApprovalTransaksi::create([
            'id_transaksi'  => $this->id_transaksi,
            'id_ahli_gizi'  => $ahliGiziId,
            'id_kepala_dapur' => $kepalaDapurId,
            'keterangan'    => $keterangan,
            'status'        => $approvalStatus,
            'approved_at'   => $autoApprove ? now() : null,
        ]);

        $this->createStockSnapshots($approval->id_approval_transaksi);

        if ($autoApprove) {
            $this->status = 'processing';
            $this->save();
            $result = $this->processTransaction();
            return $result['success'];
        } else {
            $this->status = 'processing';
            return $this->save();
        }
    }

    public function createTransactionNow(int $ahliGiziId, int $kepalaDapurId, string $keterangan = null): array
    {
        $result = [
            'success'        => false,
            'message'        => '',
            'shortages'      => [],
            'has_shortage'   => false,
        ];

        if ($this->status !== 'draft') {
            $result['message'] = 'Transaksi hanya bisa dibuat dari status draft';
            return $result;
        }

        $stockCheck = $this->checkStockWithReservations();

        $approval = ApprovalTransaksi::create([
            'id_transaksi'    => $this->id_transaksi,
            'id_ahli_gizi'    => $ahliGiziId,
            'id_kepala_dapur' => $kepalaDapurId,
            'keterangan'      => $keterangan,
            'status'          => 'approved',
            'approved_at'     => now(),
        ]);

        $this->createStockSnapshots($approval->id_approval_transaksi);

        $this->status = 'completed';
        $this->save();

        if (!$stockCheck['can_produce']) {
            $this->createShortageReport(false);
            $result['shortages']    = $stockCheck['shortages'];
            $result['has_shortage'] = true;
            $result['success']      = true;
            $result['message']      = 'Transaksi berhasil dibuat. Stok tidak mencukupi — laporan kekurangan telah dikirim ke Kepala Dapur. Transaksi akan otomatis masuk produksi setelah stok tercukupi.';
            $this->sendToProduksi(OrderProduksi::STATUS_STOK_KURANG);
            return $result;
        }

        $this->sendToProduksi();

        $result['success'] = true;
        $result['message'] = 'Transaksi berhasil dibuat dan langsung dikirim ke tim Produksi.';

        return $result;
    }

    public function deductStockForProduction(): array
    {
        $result = [
            'success'   => false,
            'message'   => '',
            'shortages' => [],
        ];

        $stockCheck = $this->checkAllStockAvailability();

        if (!$stockCheck['can_produce']) {
            $result['message']   = 'Stok tidak mencukupi untuk pengurangan produksi';
            $result['shortages'] = $stockCheck['shortages'];
            return $result;
        }

        try {
            foreach ($this->detailTransaksiDapur as $detail) {
                $detail->reduceStockFromProduction();
            }

            $result['success'] = true;
            $result['message'] = 'Stok berhasil dikurangkan';
        } catch (\Exception $e) {
            Log::error('Stock reduction failed for transaction', [
                'id_transaksi' => $this->id_transaksi,
                'error'        => $e->getMessage(),
            ]);
            $result['message'] = 'Gagal mengurangi stok: ' . $e->getMessage();
        }

        return $result;
    }

    public function processTransaction(): array
    {
        $result = [
            'success'   => false,
            'message'   => '',
            'shortages' => [],
        ];

        if ($this->status !== 'processing') {
            $result['message'] = 'Transaksi hanya bisa diproses dari status processing';
            return $result;
        }

        $approval   = $this->approvalTransaksi;
        $stockCheck = $approval
            ? $this->checkStockWithSnapshots($approval)
            : $this->checkAllStockAvailability();

        if (!$stockCheck['can_produce']) {
            $result['message']   = 'Stock tidak mencukupi untuk produksi';
            $result['shortages'] = $stockCheck['shortages'];
            return $result;
        }

        try {
            foreach ($this->detailTransaksiDapur as $detail) {
                $detail->reduceStockFromProduction();
            }

            $this->status = 'completed';
            $this->save();

            $result['success'] = true;
            $result['message'] = 'Transaksi berhasil diproses';
        } catch (\Exception $e) {
            $this->status = 'processing';
            $this->save();

            Log::error('Transaction processing failed', [
                'transaction_id' => $this->id_transaksi,
                'error'          => $e->getMessage(),
            ]);

            $result['message'] = 'Terjadi error saat memproses transaksi: ' . $e->getMessage();
        }

        return $result;
    }

    public function calculateTotalPorsi(): int
    {
        $total            = $this->detailTransaksiDapur()->where('tipe_porsi', 'besar')->value('jumlah_porsi') ?? 0;
        $this->total_porsi = $total;
        $this->save();
        return (int) $total;
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'draft' && $this->detailTransaksiDapur()->count() > 0;
    }

    public function canBeSubmittedForApproval(): bool
    {
        return $this->status === 'draft'
            && $this->detailTransaksiDapur()->count() > 0
            && !$this->approvalTransaksi;
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['draft', 'processing'])) {
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }

    public function getStatusText(): string
    {
        return match ($this->status) {
            'draft'      => 'Draft',
            'processing' => 'Menunggu Persetujuan',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => 'Unknown',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft'      => 'bg-label-secondary',
            'processing' => 'bg-label-warning',
            'completed'  => 'bg-label-success',
            'cancelled'  => 'bg-label-danger',
            default      => 'bg-label-secondary',
        };
    }

    public function getMenuDetails(): array
    {
        $menuDetails = [];

        foreach ($this->detailTransaksiDapur as $detail) {
            $requiredIngredients = $detail->menuMakanan->calculateRequiredIngredients($detail->jumlah_porsi);

            $menuDetails[] = [
                'menu'               => $detail->menuMakanan,
                'detail'             => $detail,
                'ingredients'        => $requiredIngredients,
                'total_ingredients'  => count($requiredIngredients),
                'formatted_portions' => $detail->jumlah_porsi . ' ' . $detail->getTipePorsiText(),
            ];
        }

        return $menuDetails;
    }

    public function createStockSnapshots(int $approvalId): bool
    {
        try {
            $stockCheck = $this->checkAllStockAvailability();

            foreach ($stockCheck['ingredients_summary'] as $ingredient) {
                StockSnapshot::create([
                    'id_approval_transaksi' => $approvalId,
                    'id_template_item'      => $ingredient['id_template_item'],
                    'available'             => $ingredient['available'],
                    'satuan'                => $ingredient['satuan'],
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create stock snapshots', [
                'transaction_id' => $this->id_transaksi,
                'approval_id'    => $approvalId,
                'error'          => $e->getMessage(),
            ]);
            return false;
        }
    }
}
