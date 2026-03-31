<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';
    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'id_user_role',
        'nik_pemilik',
        'nama_pemilik',
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
        return $this->belongsTo(UserRole::class, 'id_user_role')
            ->where('role_type', 'mitra');
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

    /**
     * Semua dapur yang dikaitkan dengan mitra ini (berbagai status).
     */
    public function dapur()
    {
        return $this->belongsToMany(Dapur::class, 'mitra_dapur', 'id_mitra', 'id_dapur')
            ->withPivot('id', 'status', 'catatan', 'approved_at')
            ->withTimestamps();
    }

    /**
     * Dapur yang sudah diapprove.
     */
    public function dapurApproved()
    {
        return $this->dapur()->wherePivot('status', 'approved');
    }

    /**
     * Binding dapur (pivot records).
     */
    public function mitraDapur()
    {
        return $this->hasMany(MitraDapur::class, 'id_mitra');
    }

    public function getFullWilayahAttribute(): string
    {
        $parts = array_filter([
            $this->village_name,
            $this->district_name,
            $this->regency_name,
            $this->province_name,
        ], fn($p) => !is_null($p) && $p !== '');
        return $parts ? implode(', ', $parts) : '';
    }
}
