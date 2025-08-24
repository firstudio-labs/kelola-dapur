<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSnapshot extends Model
{
    use HasFactory;

    protected $table = 'stock_snapshots';
    protected $primaryKey = 'id_stock_snapshot';

    protected $fillable = [
        'id_approval_transaksi',
        'id_template_item',
        'available',
        'satuan',
    ];

    protected $casts = [
        'available' => 'decimal:2',
    ];

    // Relationships
    public function approvalTransaksi()
    {
        return $this->belongsTo(ApprovalTransaksi::class, 'id_approval_transaksi');
    }

    public function templateItem()
    {
        return $this->belongsTo(TemplateItem::class, 'id_template_item');
    }
}
