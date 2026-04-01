<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaMbg extends Model
{
    use HasFactory;

    protected $table = 'penerima_mbg';
    protected $primaryKey = 'id_penerima';

    protected $fillable = [
        'id_user_role',
        'id_dapur',
        'id_type',
        'id_number',
        'penanggung_jawab',
        'province_code',
        'province_name',
        'regency_code',
        'regency_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'alamat_detail',
        'latitude',
        'longitude',
        'link_gmaps',
        'foto_lokasi',
        'foto_diri',
        'jumlah_porsi',
        'status_approval',
        'catatan_approval',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'jumlah_porsi' => 'integer',
    ];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role', 'id_user_role');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function isPending(): bool
    {
        return $this->status_approval === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status_approval === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status_approval === 'rejected';
    }

    public function getIdTypeLabelAttribute(): string
    {
        return match($this->id_type) {
            'nik' => 'NIK',
            'nisn' => 'NISN',
            'no_registrasi' => 'No. Registrasi',
            default => strtoupper($this->id_type),
        };
    }
}
