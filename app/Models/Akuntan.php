<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akuntan extends Model
{
    use HasFactory;

    protected $table = 'akuntan';
    protected $primaryKey = 'id_akuntan';
    
    protected $fillable = [
        'id_user_role',
        'id_dapur',
        'nik_akuntan',
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
}
