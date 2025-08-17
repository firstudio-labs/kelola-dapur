<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $table = 'stock_items';
    protected $primaryKey = 'id_stock_item';

    protected $fillable = [
        'id_dapur',
        'id_template_item',
        'jumlah',
        'satuan',
        'tanggal_restok',
        'keterangan'
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'tanggal_restok' => 'date',
    ];

    // Relationships
    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function templateItem()
    {
        return $this->belongsTo(TemplateItem::class, 'id_template_item');
    }

    public function approvalStockItems(): HasMany
    {
        return $this->hasMany(ApprovalStockItem::class, 'id_stock_item');
    }

    // Helper methods
    public function isLowStock(int $threshold = 10): bool
    {
        return (float) $this->jumlah <= $threshold;
    }

    public function reduceStock(float $amount): bool
    {
        $currentStock = (float) $this->jumlah;
        if ($currentStock >= $amount) {
            $this->jumlah = $currentStock - $amount;
            return $this->save();
        }
        return false;
    }

    public function addStock(float $amount): bool
    {
        $currentStock = (float) $this->jumlah;
        $this->jumlah = $currentStock + $amount;
        return $this->save();
    }

    public function getStockStatus(): string
    {
        $stock = (float) $this->jumlah;
        if ($stock == 0) return 'habis';
        if ($stock <= 10) return 'rendah';
        return 'normal';
    }
}
