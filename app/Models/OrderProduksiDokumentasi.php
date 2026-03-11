<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderProduksiDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'order_produksi_dokumentasi';
    protected $primaryKey = 'id_dokumentasi';

    protected $fillable = [
        'id_order',
        'path_gambar',
    ];

    public function orderProduksi()
    {
        return $this->belongsTo(OrderProduksi::class, 'id_order', 'id_order');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path_gambar);
    }
}
