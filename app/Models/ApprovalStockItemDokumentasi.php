<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalStockItemDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'approval_stock_item_dokumentasis';
    protected $primaryKey = 'id_dokumentasi';

    protected $fillable = [
        'id_approval_stock_item',
        'id_approval_stock_item_supplier',
        'foto_path'
    ];

    public function approvalStockItem()
    {
        return $this->belongsTo(ApprovalStockItem::class, 'id_approval_stock_item', 'id_approval_stock_item');
    }

    public function supplierDetail()
    {
        return $this->belongsTo(ApprovalStockItemSupplier::class, 'id_approval_stock_item_supplier');
    }
}
