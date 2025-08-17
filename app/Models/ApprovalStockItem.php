<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStockItem extends Model
{
    use HasFactory;

    protected $table = 'approval_stock_items';
    protected $primaryKey = 'id_approval_stock_item';

    protected $fillable = [
        'id_admin_gudang',
        'id_kepala_dapur',
        'id_stock_item',
        'jumlah',
        'satuan',
        'status',
        'keterangan',
        'approved_at'
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function adminGudang()
    {
        return $this->belongsTo(AdminGudang::class, 'id_admin_gudang');
    }

    public function kepalaDapur()
    {
        return $this->belongsTo(KepalaDapur::class, 'id_kepala_dapur');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'id_stock_item');
    }

    public function approve(): bool
    {
        $this->status = 'approved';
        $this->approved_at = now();

        if ($this->save()) {
            return $this->stockItem->addStock($this->jumlah);
        }
        return false;
    }

    public function reject(): bool
    {
        $this->status = 'rejected';
        $this->approved_at = now();
        return $this->save();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
