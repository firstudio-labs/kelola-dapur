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
        'approved_at',
        'jam_kedatangan',
        'tanggal_produksi',
        'tanggal_expired',
        'suhu_bahan_makanan',
        'warna_bahan_makanan',
        'foto_bahan'
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'approved_at' => 'datetime',
        'tanggal_produksi' => 'date',
        'tanggal_expired' => 'date',
        'suhu_bahan_makanan' => 'decimal:2',
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
            // Update stock dengan tanggal_restok secara eksplisit
            $stockItem = $this->stockItem;
            $currentStock = (float) $stockItem->jumlah;
            return StockItem::where('id_stock_item', $stockItem->id_stock_item)
                ->update([
                    'jumlah' => $currentStock + $this->jumlah,
                    'tanggal_restok' => now()
                ]);
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
