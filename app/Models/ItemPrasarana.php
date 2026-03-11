<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrasarana extends Model
{
    use HasFactory;

    protected $table = 'item_prasarana';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_kategori',
        'nama_item',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriPrasarana::class, 'id_kategori', 'id_kategori');
    }

    public function dapurPrasarana()
    {
        return $this->hasMany(DapurPrasarana::class, 'id_item', 'id_item');
    }
}
