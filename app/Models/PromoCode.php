<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';
    protected $primaryKey = 'id_promo';

    protected $fillable = [
        'kode_promo',
        'persentase_diskon',
        'tanggal_mulai',
        'tanggal_berakhir',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'is_active' => 'boolean'
    ];

    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class, 'id_promo');
    }

    // Helper methods
    public function isValid(): bool
    {
        $now = Carbon::now()->format('Y-m-d');

        return $this->is_active &&
            $this->tanggal_mulai <= $now &&
            $this->tanggal_berakhir >= $now;
    }

    public function isExpired(): bool
    {
        return Carbon::now()->format('Y-m-d') > $this->tanggal_berakhir;
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if (Carbon::now()->format('Y-m-d') < $this->tanggal_mulai) {
            return 'upcoming';
        }

        return 'active';
    }

    public function getStatusTextAttribute(): string
    {
        switch ($this->status) {
            case 'inactive':
                return 'Tidak Aktif';
            case 'expired':
                return 'Kadaluarsa';
            case 'upcoming':
                return 'Belum Dimulai';
            case 'active':
                return 'Aktif';
            default:
                return 'Unknown';
        }
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now()->format('Y-m-d');
        return $query->where('is_active', true)
            ->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_berakhir', '>=', $now);
    }
}
