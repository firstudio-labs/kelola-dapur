<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sarpas extends Model
{
    use HasFactory;

    protected $table = 'sarpas';
    protected $primaryKey = 'id_sarpas';

    protected $fillable = [
        'id_user_role',
        'id_dapur',
        'nik_sarpas',
        'nama_lengkap',
        'alamat_detail',
        'province_code',
        'province_name',
        'regency_code',
        'regency_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'kontak_wa',
        'pendidikan',
        'jenis_kelamin',
        'foto_diri',
        'jabatan',
    ];

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'id_user_role', 'id_user_role');
    }

    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }
}
