<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';

    protected $fillable = [
        'id_user_role',
        'id_dapur',
        'nik_produksi',
        'nama_lengkap',
        'kontak_wa',
        'pendidikan',
        'jenis_kelamin',
        'foto_diri',
        'jabatan',
        'province_code',
        'province_name',
        'regency_code',
        'regency_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'alamat_detail',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role', 'id_user_role');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function isKepalaProduksi(): bool
    {
        return $this->jabatan === 'Penanggung jawab';
    }
}
