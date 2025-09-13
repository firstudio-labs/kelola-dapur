<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SubscriptionRequest extends Model
{
    use HasFactory;

    protected $table = 'subscription_requests';
    protected $primaryKey = 'id_subscription_request';

    protected $fillable = [
        'id_dapur',
        'id_package',
        'id_promo',
        'harga_asli',
        'diskon',
        'harga_final',
        'bukti_transfer',
        'status',
        'catatan',
        'tanggal_request',
        'tanggal_approval'
    ];

    protected $casts = [
        'harga_asli' => 'integer',
        'diskon' => 'integer',
        'harga_final' => 'integer',
        'tanggal_request' => 'datetime',
        'tanggal_approval' => 'datetime'
    ];

    // Relationships
    public function dapur(): BelongsTo
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class, 'id_package');
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'id_promo');
    }

    // Helper methods
    public function getFormattedHargaAsliAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_asli, 0, ',', '.');
    }

    public function getFormattedDiskonAttribute(): string
    {
        return 'Rp ' . number_format($this->diskon, 0, ',', '.');
    }

    public function getFormattedHargaFinalAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_final, 0, ',', '.');
    }

    public function getStatusTextAttribute(): string
    {
        switch ($this->status) {
            case 'pending':
                return 'Menunggu Approval';
            case 'approved':
                return 'Disetujui';
            case 'rejected':
                return 'Ditolak';
            default:
                return 'Unknown';
        }
    }

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'pending':
                return 'warning';
            case 'approved':
                return 'success';
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function approve(?string $catatan = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'catatan' => $catatan,
            'tanggal_approval' => now()
        ]);

        // Update subscription_end di dapur
        $newEndDate = $this->dapur->subscription_end && $this->dapur->subscription_end > now()
            ? Carbon::parse($this->dapur->subscription_end)->addDays($this->package->durasi_hari)
            : Carbon::now()->addDays($this->package->durasi_hari);

        $this->dapur->update([
            'subscription_end' => $newEndDate->format('Y-m-d'),
            'status' => 'active'
        ]);

        return true;
    }

    public function reject(?string $catatan = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'catatan' => $catatan,
            'tanggal_approval' => now()
        ]);

        return true;
    }

    public static function calculatePrice(SubscriptionPackage $package, int $dapurId, ?PromoCode $promoCode = null): array
    {
        $hargaAsli = $package->harga;
        $diskon = 0;

        if ($promoCode && $promoCode->isValid()) {
            $diskon = ($hargaAsli * $promoCode->persentase_diskon) / 100;
        }

        $hargaSetelahDiskon = $hargaAsli - $diskon;
        $hargaFinal = $hargaSetelahDiskon + $dapurId;

        return [
            'harga_asli' => $hargaAsli,
            'diskon' => $diskon,
            'harga_final' => $hargaFinal
        ];
    }

    // Scope methods
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
