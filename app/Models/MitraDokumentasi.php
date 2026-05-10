<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDokumentasi extends Model
{
    use HasFactory;

    protected $table = 'mitra_dokumentasis';
    protected $primaryKey = 'id_dokumentasi';

    protected $fillable = [
        'id_mitra',
        'id_dapur',
        'tanggal_waktu',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function fotos()
    {
        return $this->hasMany(MitraDokumentasiFoto::class, 'id_dokumentasi', 'id_dokumentasi');
    }
}
