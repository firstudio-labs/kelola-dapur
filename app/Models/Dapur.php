<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dapur extends Model
{
    use HasFactory;

    protected $table = 'dapur';
    protected $primaryKey = 'id_dapur';

    protected $fillable = [
        'nama_dapur',
        // 'wilayah',
        'province_code',
        'province_name',
        'regency_code',
        'regency_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'alamat',
        'telepon',
        'status',
        'subscription_end'
    ];

    protected $casts = [
        'subscription_end' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'id_dapur';
    }

    // Relationships
    public function kepalaDapur(): HasMany
    {
        return $this->hasMany(KepalaDapur::class, 'id_dapur');
    }

    public function adminGudang(): HasMany
    {
        return $this->hasMany(AdminGudang::class, 'id_dapur');
    }

    public function ahliGizi(): HasMany
    {
        return $this->hasMany(AhliGizi::class, 'id_dapur');
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'id_dapur');
    }

    public function transaksiDapur(): HasMany
    {
        return $this->hasMany(TransaksiDapur::class, 'id_dapur');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getLowStockItems(int $threshold = 10)
    {
        return $this->stockItems()
            ->where('jumlah', '<=', $threshold)
            ->with('templateItem')
            ->get();
    }

    public function getTotalStockValue(): int
    {
        return $this->stockItems()->sum('jumlah');
    }

    public function getActiveUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kepala_dapur', 'id_dapur', 'id_user')
            ->orWhere(function ($query) {
                $query->belongsToMany(User::class, 'admin_gudang', 'id_dapur', 'id_user');
            })
            ->orWhere(function ($query) {
                $query->belongsToMany(User::class, 'ahli_gizi', 'id_dapur', 'id_user');
            })
            ->where('users.is_active', true);
    }

    // Wilayah helper methods
    public function getFullWilayahAttribute(): string
    {
        $parts = array_filter([
            $this->village_name ?: $this->district_name,
            $this->regency_name,
            $this->province_name
        ]);

        return implode(', ', $parts);
    }

    public function getWilayahHierarchyAttribute(): array
    {
        return [
            'province' => [
                'code' => $this->province_code,
                'name' => $this->province_name
            ],
            'regency' => [
                'code' => $this->regency_code,
                'name' => $this->regency_name
            ],
            'district' => [
                'code' => $this->district_code,
                'name' => $this->district_name
            ],
            'village' => [
                'code' => $this->village_code,
                'name' => $this->village_name
            ]
        ];
    }

    // Scope methods for filtering by wilayah
    public function scopeByProvince($query, $provinceCode)
    {
        return $query->where('province_code', $provinceCode);
    }

    public function scopeByRegency($query, $regencyCode)
    {
        return $query->where('regency_code', $regencyCode);
    }

    public function scopeByDistrict($query, $districtCode)
    {
        return $query->where('district_code', $districtCode);
    }

    public function scopeByVillage($query, $villageCode)
    {
        return $query->where('village_code', $villageCode);
    }

    // Search scope for wilayah
    public function scopeSearchWilayah($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('province_name', 'like', "%{$search}%")
                ->orWhere('regency_name', 'like', "%{$search}%")
                ->orWhere('district_name', 'like', "%{$search}%")
                ->orWhere('village_name', 'like', "%{$search}%")
                ->orWhere('wilayah', 'like', "%{$search}%");
        });
    }
}
