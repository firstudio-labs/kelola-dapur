<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $table = 'subscription_packages';
    protected $primaryKey = 'id_package';

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
        'durasi_hari',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga' => 'integer'
    ];

    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class, 'id_package');
    }

    // Helper methods
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getDurasiTextAttribute(): string
    {
        if ($this->durasi_hari >= 30) {
            $bulan = floor($this->durasi_hari / 30);
            $hari = $this->durasi_hari % 30;

            $text = $bulan . ' bulan';
            if ($hari > 0) {
                $text .= ' ' . $hari . ' hari';
            }
            return $text;
        }

        return $this->durasi_hari . ' hari';
    }

    public function calculateFinalPrice(int $dapurId, ?PromoCode $promoCode = null): int
    {
        $basePrice = $this->harga;

        // Apply promo discount
        if ($promoCode && $promoCode->isValid()) {
            $discount = ($basePrice * $promoCode->persentase_diskon) / 100;
            $basePrice -= $discount;
        }

        // Add dapur ID to final price
        return $basePrice + $dapurId;
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
