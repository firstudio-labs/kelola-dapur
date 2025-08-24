<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function latestApprovedRequest(): HasOne
    {
        return $this->hasOne(ApprovalStockItem::class, 'id_stock_item')
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(1);
    }

    public function getSatuanAttribute($value)
    {
        if ($this->relationLoaded('templateItem') && $this->templateItem) {
            return $this->templateItem->satuan;
        }

        if ($this->id_template_item) {
            $templateItem = $this->templateItem()->first();
            if ($templateItem) {
                return $templateItem->satuan;
            }
        }

        return $value;
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

    /**
     * Get stock status badge class for display
     */
    public function getStockStatusBadgeClass(): string
    {
        return match ($this->getStockStatus()) {
            'habis' => 'bg-danger',
            'rendah' => 'bg-warning',
            'normal' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Get formatted stock amount with unit from TemplateItem
     */
    public function getFormattedStock(): string
    {
        return number_format($this->jumlah, 3) . ' ' . $this->satuan;
    }

    /**
     * Check if stock is sufficient for given amount
     */
    public function isSufficient(float $requiredAmount): bool
    {
        return (float) $this->jumlah >= $requiredAmount;
    }

    /**
     * Get shortage amount if stock is insufficient
     */
    public function getShortageAmount(float $requiredAmount): float
    {
        $available = (float) $this->jumlah;
        return $requiredAmount > $available ? $requiredAmount - $available : 0;
    }

    /**
     * Create stock snapshot for approval
     */
    public function createSnapshot(int $approvalId): ?StockSnapshot
    {
        return StockSnapshot::create([
            'id_approval_transaksi' => $approvalId,
            'id_template_item' => $this->id_template_item,
            'available' => $this->jumlah,
            'satuan' => $this->satuan,
        ]);
    }
    public function getLatestRestockDate()
    {
        if ($this->relationLoaded('latestApprovedRequest') && $this->latestApprovedRequest) {
            return $this->latestApprovedRequest->approved_at;
        }

        $latestApproval = $this->approvalStockItems()
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->first();

        return $latestApproval ? $latestApproval->approved_at : $this->tanggal_restok;
    }

    /**
     * Boot method to sync satuan with TemplateItem when saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($stockItem) {
            if ($stockItem->id_template_item) {
                $templateItem = TemplateItem::find($stockItem->id_template_item);
                if ($templateItem) {
                    $stockItem->attributes['satuan'] = $templateItem->satuan;
                }
            }
        });
    }
}
