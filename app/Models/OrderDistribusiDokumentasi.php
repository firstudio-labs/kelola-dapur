<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDistribusiDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi_dokumentasi';
    protected $primaryKey = 'id_dokumentasi_distribusi';

    protected $fillable = [
        'id_distribusi',
        'path_gambar',
    ];

    public function orderDistribusi()
    {
        return $this->belongsTo(OrderDistribusi::class, 'id_distribusi', 'id_distribusi');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path_gambar);
    }
}
