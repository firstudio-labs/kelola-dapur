<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
                        'total_needed' => 0
                    ];
                }

                $allIngredients[$key]['total_needed'] += $ingredient['total_needed'];
            }
        }

        foreach ($allIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $available = $stockItem ? (float)$stockItem->jumlah : 0;
            $needed = $ingredient['total_needed'];

            $ingredientData = [
                'id_template_item' => $ingredient['id_template_item'],
                'nama_bahan' => $ingredient['nama_bahan'],
                'satuan' => $ingredient['satuan'],
                'needed' => $needed,
                'available' => $available,
                'sufficient' => $available >= $needed
            ];

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

        ApprovalTransaksi::create([
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

        $stockCheck = $this->checkAllStockAvailability();

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
        } catch (\Exception $e) {
            $this->status = 'processing';
            $this->save();

            $result['message'] = 'Terjadi error saat memproses transaksi: ' . $e->getMessage();
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
}
