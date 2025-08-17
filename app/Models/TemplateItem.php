<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateItem extends Model
{
    use HasFactory;

    protected $table = 'template_items';
    protected $primaryKey = 'id_template_item';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'keterangan'
    ];

    // Relationships
    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'id_template_item');
    }

    public function bahanMenu(): HasMany
    {
        return $this->hasMany(BahanMenu::class, 'id_template_item');
    }

    // Helper methods
    public function getStockByDapur(int $dapurId)
    {
        return $this->stockItems()->where('id_dapur', $dapurId)->first();
    }

    public function getTotalStockAllDapur(): float
    {
        return $this->stockItems()->sum('jumlah');
    }
}
