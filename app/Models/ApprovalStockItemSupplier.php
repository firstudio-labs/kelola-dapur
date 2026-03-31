<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStockItemSupplier extends Model
{
    use HasFactory;

    protected $table = 'approval_stock_item_suppliers';

    protected $fillable = [
        'id_approval_stock_item',
        'id_supplier',
        'jumlah',
    ];

    public function approvalStockItem()
    {
        return $this->belongsTo(ApprovalStockItem::class, 'id_approval_stock_item', 'id_approval_stock_item');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function dokumentasi()
    {
        return $this->hasMany(ApprovalStockItemDokumentasi::class, 'id_approval_stock_item_supplier');
    }
}
