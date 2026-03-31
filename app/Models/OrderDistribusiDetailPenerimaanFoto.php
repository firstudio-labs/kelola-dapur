<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDistribusiDetailPenerimaanFoto extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi_detail_penerimaan_foto';

    protected $fillable = [
        'id_detail',
        'path_foto',
    ];

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path_foto);
    }

    public function detail()
    {
        return $this->belongsTo(OrderDistribusiDetail::class, 'id_detail', 'id_detail');
    }
}
