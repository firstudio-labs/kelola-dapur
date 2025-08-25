<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuMakanan extends Model
{
    use HasFactory;

    protected $table = 'menu_makanan';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nama_menu',
        'gambar_menu',
        'deskripsi',
        'kategori',
        'is_active',
        'created_by_dapur_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['gambar_url', 'gambar_full_path'];

    public const KATEGORI_OPTIONS = [
        'Karbohidrat' => 'Karbohidrat',
        'Lauk' => 'Lauk',
        'Sayur' => 'Sayur',
        'Tambahan' => 'Tambahan'
    ];

    // Relationships
    public function createdByDapur()
    {
        return $this->belongsTo(Dapur::class, 'created_by_dapur_id', 'id_dapur');
    }

    public function bahanMenu(): HasMany
    {
        return $this->hasMany(BahanMenu::class, 'id_menu');
    }

    public function detailTransaksiDapur(): HasMany
    {
        return $this->hasMany(DetailTransaksiDapur::class, 'id_menu');
    }

    // Image handling
    public function getGambarUrlAttribute(): ?string
    {
        if (!$this->gambar_menu) {
            return asset('images/menu/default-menu.jpg');
        }

        if (Storage::disk('public')->exists('menu/' . $this->gambar_menu)) {
            return asset('storage/menu/' . $this->gambar_menu);
        }

        if (file_exists(public_path('images/menu/' . $this->gambar_menu))) {
            return asset('images/menu/' . $this->gambar_menu);
        }

        return asset('images/menu/default-menu.jpg');
    }

    public function getGambarFullPathAttribute(): ?string
    {
        if (!$this->gambar_menu) {
            return null;
        }

        if (Storage::disk('public')->exists('menu/' . $this->gambar_menu)) {
            return storage_path('app/public/menu/' . $this->gambar_menu);
        }

        return public_path('images/menu/' . $this->gambar_menu);
    }

    public function deleteGambar(): bool
    {
        if ($this->gambar_menu && Storage::disk('public')->exists('menu/' . $this->gambar_menu)) {
            return Storage::disk('public')->delete('menu/' . $this->gambar_menu);
        }
        return false;
    }
    public function hasGambar(): bool
    {
        return !empty($this->gambar_menu);
    }
    public function calculateRequiredIngredients(int $jumlahPorsi): array
    {
        $ingredients = [];

        foreach ($this->bahanMenu as $bahan) {
            $totalNeeded = $bahan->getTotalKebutuhan($jumlahPorsi);
            $beratBasahPerPorsi = $bahan->getBeratBasah();
            $totalBeratBasah = $bahan->getTotalBeratBasah($jumlahPorsi);

            $ingredients[] = [
                'id_template_item' => $bahan->id_template_item,
                'nama_bahan' => $bahan->templateItem->nama_bahan ?? 'Unknown',
                'satuan' => $bahan->templateItem->satuan ?? '',
                'jumlah_per_porsi' => (float) $bahan->jumlah_per_porsi,
                'total_needed' => $totalNeeded,
                'is_bahan_basah' => $bahan->is_bahan_basah,
                'berat_basah_per_porsi' => $beratBasahPerPorsi,
                'total_berat_basah' => $totalBeratBasah,
                'keterangan' => $bahan->templateItem->keterangan ?? ''
            ];
        }

        return $ingredients;
    }
    public function getTotalIngredientsCount(): int
    {
        return $this->bahanMenu()->count();
    }
    public function isReadyForProduction(): bool
    {
        return $this->is_active && $this->bahanMenu()->count() > 0;
    }
    public function getKategoriBadgeClass(): string
    {
        return match ($this->kategori) {
            'Karbohidrat' => 'bg-label-primary',
            'Lauk' => 'bg-label-success',
            'Sayur' => 'bg-label-info',
            'Tambahan' => 'bg-label-warning',
            default => 'bg-label-secondary'
        };
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
