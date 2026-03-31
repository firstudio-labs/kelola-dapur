<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'id_dapur',
        'nama_supplier',
        'kontak',
        'alamat',
        'keterangan'
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function approvalStockItems()
    {
        return $this->hasMany(ApprovalStockItem::class, 'id_supplier');
    }
}
