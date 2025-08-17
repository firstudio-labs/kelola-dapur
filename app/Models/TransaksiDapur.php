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

    // Helper methods
    public function processTransaction(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'shortages' => []
        ];

        if ($this->status !== 'draft') {
            $result['message'] = 'Transaksi hanya bisa diproses dari status draft';
            return $result;
        }

        $allAvailable = true;
        $allShortages = [];

        foreach ($this->detailTransaksiDapur as $detail) {
            $stockCheck = $detail->menuMakanan->checkStockAvailability(
                $detail->jumlah_porsi,
                $this->id_dapur
            );

            if (!$stockCheck['can_produce']) {
                $allAvailable = false;
                $allShortages = array_merge($allShortages, $stockCheck['shortage']);
            }
        }

        if (!$allAvailable) {
            $result['message'] = 'Stock tidak mencukupi untuk produksi';
            $result['shortages'] = $allShortages;
            return $result;
        }

        $this->status = 'processing';
        $this->save();

        try {
            foreach ($this->detailTransaksiDapur as $detail) {
                $detail->reduceStockFromProduction();
            }

            $this->status = 'completed';
            $this->save();

            $result['success'] = true;
            $result['message'] = 'Transaksi berhasil diproses';
        } catch (\Exception $e) {
            $this->status = 'draft';
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

    public function cancel(): bool
    {
        if (in_array($this->status, ['draft', 'processing'])) {
            $this->status = 'cancelled';
            return $this->save();
        }
        return false;
    }
}
