<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhliGizi extends Model
{
    use HasFactory;

    protected $table = 'ahli_gizi';
    protected $primaryKey = 'id_ahli_gizi';

    protected $fillable = [
        'id_user_role', 
        'id_dapur',
        'nama_lengkap',
        'nik_ahli_gizi',
        'jabatan',
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
        'pendidikan_terakhir',
        'jenis_kelamin',
        'foto_diri',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'ahli_gizi');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserRole::class,
            'id_user_role', 
            'id_user', 
            'id_user_role', 
            'id_user' 
        );
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function approvalTransaksi()
    {
        return $this->hasMany(ApprovalTransaksi::class, 'id_ahli_gizi');
    }

    public function transaksiDapur()
    {
        return $this->hasMany(TransaksiDapur::class, 'created_by', 'id_user');
    }
}
