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

    // Relationships
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

    // Helper methods 
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
        return $this->detailTransaksiDapur()->where('tipe_porsi', 'besar')->sum('jumlah_porsi');
    }

    public function getTotalPorsiKecil(): int
    {
        return $this->detailTransaksiDapur()->where('tipe_porsi', 'kecil')->sum('jumlah_porsi');
    }

    public function checkAllStockAvailability(): array
    {
        $result = [
            'can_produce' => true,
            'shortages' => [],
            'ingredients_summary' => []
        ];

        $allIngredients = [];

        foreach ($this->detailTransaksiDapur as $detail) {
            $requiredIngredients = $detail->menuMakanan->calculateRequiredIngredients($detail->jumlah_porsi);

            foreach ($requiredIngredients as $ingredient) {
                $key = $ingredient['id_template_item'];

                if (!isset($allIngredients[$key])) {
                    $allIngredients[$key] = [
                        'id_template_item' => $ingredient['id_template_item'],
                        'nama_bahan' => $ingredient['nama_bahan'],
                        'satuan' => $ingredient['satuan'],
                        'is_bahan_basah' => isset($ingredient['is_bahan_basah']) ? $ingredient['is_bahan_basah'] : false,
                        'needed' => 0
                    ];
                }

                $neededToAdd = isset($ingredient['is_bahan_basah']) && $ingredient['is_bahan_basah']
                    ? $ingredient['total_berat_basah']
                    : $ingredient['total_needed'];

                $allIngredients[$key]['needed'] += $neededToAdd;

                Log::debug('Aggregating ingredient in checkAllStockAvailability', [
                    'id_transaksi' => $this->id_transaksi,
                    'nama_bahan' => $ingredient['nama_bahan'],
                    'is_bahan_basah' => $ingredient['is_bahan_basah'] ?? 'not_set',
                    'needed_to_add' => $neededToAdd
                ]);
            }
        }

        foreach ($allIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $available = $stockItem ? (float)$stockItem->jumlah : 0;
            $needed = $ingredient['needed'];

            $ingredientData = [
                'id_template_item' => $ingredient['id_template_item'],
                'nama_bahan' => $ingredient['nama_bahan'],
                'satuan' => $ingredient['satuan'],
                'is_bahan_basah' => $ingredient['is_bahan_basah'],
                'needed' => $needed,
                'available' => $available,
                'sufficient' => $available >= $needed
            ];

            Log::debug('Final ingredient data in checkAllStockAvailability', [
                'id_transaksi' => $this->id_transaksi,
                'nama_bahan' => $ingredient['nama_bahan'],
                'is_bahan_basah' => $ingredient['is_bahan_basah'],
                'needed' => $needed,
                'available' => $available
            ]);

            if ($available < $needed) {
                $result['can_produce'] = false;
                $result['shortages'][] = [
                    'id_template_item' => $ingredient['id_template_item'],
                    'nama_bahan' => $ingredient['nama_bahan'],
                    'satuan' => $ingredient['satuan'],
                    'needed' => $needed,
                    'available' => $available,
                    'shortage' => $needed - $available
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

                    $ingredient['available'] = (float)$snapshot->available;
                    $ingredient['sufficient'] = $ingredient['available'] >= $ingredient['needed'];
                    $ingredient['from_snapshot'] = true;
                } else {
                    $ingredient['from_snapshot'] = false;
                }
            }

            $stockCheck['can_produce'] = collect($stockCheck['ingredients_summary'])->every(function ($ingredient) {
                return $ingredient['sufficient'];
            });

            $stockCheck['shortages'] = collect($stockCheck['ingredients_summary'])
                ->filter(function ($ingredient) {
                    return !$ingredient['sufficient'];
                })
                ->map(function ($ingredient) {
                    return [
                        'id_template_item' => $ingredient['id_template_item'],
                        'nama_bahan' => $ingredient['nama_bahan'],
                        'satuan' => $ingredient['satuan'],
                        'needed' => $ingredient['needed'],
                        'available' => $ingredient['available'],
                        'shortage' => $ingredient['needed'] - $ingredient['available']
                    ];
                })
                ->values()
                ->toArray();
        }

        return $stockCheck;
    }

    public function createShortageReport(): bool
    {
        $stockCheck = $this->checkAllStockAvailability();

        if ($stockCheck['can_produce']) {
            return false;
        }

        $this->laporanKekuranganStock()->delete();

        foreach ($stockCheck['shortages'] as $shortage) {
            LaporanKekuranganStock::createFromShortage($this->id_transaksi, $shortage);
        }

        return true;
    }

    public function submitForApproval(int $ahliGiziId, int $kepalaDapurId, string $keterangan = null): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $stockCheck = $this->checkAllStockAvailability();

        if (!$stockCheck['can_produce']) {
            $this->createShortageReport();
            return false;
        }

        $approval = ApprovalTransaksi::create([
            'id_transaksi' => $this->id_transaksi,
            'id_ahli_gizi' => $ahliGiziId,
            'id_kepala_dapur' => $kepalaDapurId,
            'keterangan' => $keterangan,
            'status' => 'pending'
        ]);

        $this->status = 'processing';
        return $this->save();
    }

    public function processTransaction(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'shortages' => []
        ];

        if ($this->status !== 'processing') {
            $result['message'] = 'Transaksi hanya bisa diproses dari status processing';
            return $result;
        }

        $approval = $this->approvalTransaksi;
        if ($approval) {
            $stockCheck = $this->checkStockWithSnapshots($approval);
        } else {
            $stockCheck = $this->checkAllStockAvailability();
        }

        if (!$stockCheck['can_produce']) {
            $result['message'] = 'Stock tidak mencukupi untuk produksi';
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

            Log::info('Transaction processed successfully', [
                'transaction_id' => $this->id_transaksi,
                'total_porsi' => $this->total_porsi,
                'used_snapshots' => $stockCheck['has_snapshots'] ?? false
            ]);
        } catch (\Exception $e) {
            $this->status = 'processing';
            $this->save();

            $result['message'] = 'Terjadi error saat memproses transaksi: ' . $e->getMessage();

            Log::error('Transaction processing failed', [
                'transaction_id' => $this->id_transaksi,
                'error' => $e->getMessage()
            ]);
        }

        return $result;
    }

    public function calculateTotalPorsi(): int
    {
        $total = $this->detailTransaksiDapur()->sum('jumlah_porsi');
        $this->total_porsi = $total;
        $this->save();
        return $total;
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'draft' && $this->detailTransaksiDapur()->count() > 0;
    }

    public function canBeSubmittedForApproval(): bool
    {
        return $this->status === 'draft' &&
            $this->detailTransaksiDapur()->count() > 0 &&
            !$this->approvalTransaksi;
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
            'draft' => 'Draft',
            'processing' => 'Menunggu Persetujuan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'bg-label-secondary',
            'processing' => 'bg-label-warning',
            'completed' => 'bg-label-success',
            'cancelled' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Get detailed menu information for this transaction
     */
    public function getMenuDetails(): array
    {
        $menuDetails = [];

        foreach ($this->detailTransaksiDapur as $detail) {
            $requiredIngredients = $detail->menuMakanan->calculateRequiredIngredients($detail->jumlah_porsi);

            $menuDetails[] = [
                'menu' => $detail->menuMakanan,
                'detail' => $detail,
                'ingredients' => $requiredIngredients,
                'total_ingredients' => count($requiredIngredients),
                'formatted_portions' => $detail->jumlah_porsi . ' ' . $detail->getTipePorsiText()
            ];
        }

        return $menuDetails;
    }

    /**
     * Create stock snapshots for this transaction
     */
    public function createStockSnapshots(int $approvalId): bool
    {
        try {
            $stockCheck = $this->checkAllStockAvailability();

            foreach ($stockCheck['ingredients_summary'] as $ingredient) {
                StockSnapshot::create([
                    'id_approval_transaksi' => $approvalId,
                    'id_template_item' => $ingredient['id_template_item'],
                    'available' => $ingredient['available'],
                    'satuan' => $ingredient['satuan']
                ]);
            }

            Log::info('Stock snapshots created for transaction', [
                'transaction_id' => $this->id_transaksi,
                'approval_id' => $approvalId,
                'snapshots_count' => count($stockCheck['ingredients_summary'])
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create stock snapshots', [
                'transaction_id' => $this->id_transaksi,
                'approval_id' => $approvalId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
