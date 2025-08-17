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
        'jumlah_porsi'
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

        foreach ($requiredIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $this->transaksiDapur->id_dapur)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            if ($stockItem) {
                if (!$stockItem->reduceStock($ingredient['total_needed'])) {
                    throw new \Exception("Gagal mengurangi stock untuk {$ingredient['nama_bahan']}");
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

    // Auto update total porsi when detail is saved
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
