<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminGudang extends Model
{
    use HasFactory;

    protected $table = 'admin_gudang';
    protected $primaryKey = 'id_admin_gudang';

    protected $fillable = [
        'id_user_role', 
        'id_dapur',
        'nik_admin_gudang',
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
        'pendidikan_terakhir',
        'jenis_kelamin',
        'foto_diri',
        'jabatan',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'admin_gudang');
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

    public function approvalStockItems(): HasMany
    {
        return $this->hasMany(ApprovalStockItem::class, 'id_admin_gudang');
    }
}
