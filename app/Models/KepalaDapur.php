<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KepalaDapur extends Model
{
    use HasFactory;

    protected $table = 'kepala_dapur';
    protected $primaryKey = 'id_kepala_dapur';

    protected $fillable = [
        'id_user_role', 
        'id_dapur',
        'nik_kepala_sppg',
        'alamat_detail',
        'kode_provinsi',
        'kode_kabupaten',
        'kode_kecamatan',
        'kode_desa',
        'kontak_wa',
        'pendidikan_terakhir',
        'jenis_kelamin',
        'foto_diri',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'kepala_dapur');
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
        return $this->hasMany(ApprovalStockItem::class, 'id_kepala_dapur');
    }

    public function approvalTransaksi()
    {
        return $this->hasMany(ApprovalTransaksi::class, 'id_kepala_dapur');
    }

    public function getPendingApprovals()
    {
        return $this->approvalTransaksi()->where('status', 'pending')->get();
    }

    public function getApprovalHistory()
    {
        return $this->approvalTransaksi()->whereIn('status', ['approved', 'rejected'])->get();
    }
}
