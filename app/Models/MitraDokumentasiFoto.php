<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDokumentasiFoto extends Model
{
    use HasFactory;

    protected $table = 'mitra_dokumentasi_fotos';
    protected $primaryKey = 'id_foto';

    protected $fillable = [
        'id_dokumentasi',
        'url',
    ];

    public function dokumentasi()
    {
        return $this->belongsTo(MitraDokumentasi::class, 'id_dokumentasi', 'id_dokumentasi');
    }
}
