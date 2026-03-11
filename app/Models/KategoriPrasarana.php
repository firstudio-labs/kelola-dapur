<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPrasarana extends Model
{
    use HasFactory;

    protected $table = 'kategori_prasarana';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(ItemPrasarana::class, 'id_kategori', 'id_kategori');
    }
}
