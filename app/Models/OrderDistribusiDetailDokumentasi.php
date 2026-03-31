<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderDistribusiDetailDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi_detail_dokumentasi';

    protected $fillable = [
        'id_detail',
        'path_gambar',
    ];

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path_gambar);
    }

    public function detail()
    {
        return $this->belongsTo(OrderDistribusiDetail::class, 'id_detail', 'id_detail');
    }
}
