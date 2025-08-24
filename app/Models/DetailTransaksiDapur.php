<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksiDapur extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi_dapur';
    protected $primaryKey = 'id_detail_transaksi';

    protected $fillable = [
        'id_transaksi',
        'id_menu',
        'jumlah_porsi',
        'tipe_porsi'
    ];

    protected $casts = [
        'jumlah_porsi' => 'integer',
    ];

    // Relationships
    public function transaksiDapur()
    {
        return $this->belongsTo(TransaksiDapur::class, 'id_transaksi');
    }

    public function menuMakanan()
    {
        return $this->belongsTo(MenuMakanan::class, 'id_menu');
    }

    // Helper methods
    public function reduceStockFromProduction(): bool
    {
        $requiredIngredients = $this->menuMakanan->calculateRequiredIngredients($this->jumlah_porsi);
        $approval = $this->transaksiDapur->approvalTransaksi;
        $snapshots = $approval ? StockSnapshot::where('id_approval_transaksi', $approval->id_approval_transaksi)
            ->get()
            ->keyBy('id_template_item') : collect();

        foreach ($requiredIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->transaksiDapur->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            if ($stockItem) {
                $amountToReduce = isset($ingredient['is_bahan_basah']) && $ingredient['is_bahan_basah']
                    ? $ingredient['total_berat_basah']
                    : $ingredient['total_needed'];

                if ($snapshots->has($ingredient['id_template_item'])) {
                    $snapshot = $snapshots->get($ingredient['id_template_item']);
                    if ((float)$snapshot->available < $amountToReduce) {
                        throw new \Exception("Stok snapshot tidak cukup untuk {$ingredient['nama_bahan']} (Diperlukan: {$amountToReduce}, Snapshot: {$snapshot->available})");
                    }
                }

                if (!$stockItem->reduceStock($amountToReduce)) {
                    throw new \Exception("Gagal mengurangi stock untuk {$ingredient['nama_bahan']} (Diperlukan: {$amountToReduce}, Tersedia: {$stockItem->jumlah})");
                }
            } else {
                throw new \Exception("Stock item tidak ditemukan untuk {$ingredient['nama_bahan']}");
            }
        }

        return true;
    }

    public function getRequiredIngredients(): array
    {
        return $this->menuMakanan->calculateRequiredIngredients($this->jumlah_porsi);
    }

    // Helper methods 
    public function isPorsiBesar(): bool
    {
        return $this->tipe_porsi === 'besar';
    }

    public function isPorsiKecil(): bool
    {
        return $this->tipe_porsi === 'kecil';
    }

    public function getTipePorsiText(): string
    {
        return match ($this->tipe_porsi) {
            'besar' => 'Porsi Besar',
            'kecil' => 'Porsi Kecil',
            default => 'Unknown'
        };
    }

    public function getTipePorsiBadgeClass(): string
    {
        return match ($this->tipe_porsi) {
            'besar' => 'bg-label-primary',
            'kecil' => 'bg-label-info',
            default => 'bg-label-secondary'
        };
    }

    protected static function booted()
    {
        static::saved(function ($detail) {
            $detail->transaksiDapur->calculateTotalPorsi();
        });

        static::deleted(function ($detail) {
            $detail->transaksiDapur->calculateTotalPorsi();
        });
    }
}
