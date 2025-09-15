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

    public function scopeByDapur($query, $dapurId)
    {
        return $query->where('created_by_dapur_id', $dapurId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_menu', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
        });
    }

    public static function getMenusByDapur($dapurId, $limit = null)
    {
        $query = static::where('created_by_dapur_id', $dapurId)
            ->with(['bahanMenu.templateItem', 'createdByDapur'])
            ->orderBy('nama_menu', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public static function getStatsByDapur($dapurId = null)
    {
        $query = static::query();

        if ($dapurId) {
            $query->where('created_by_dapur_id', $dapurId);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('is_active', true)->count(),
            'inactive' => $query->where('is_active', false)->count(),
            'by_category' => [
                'Karbohidrat' => $query->where('kategori', 'Karbohidrat')->count(),
                'Lauk' => $query->where('kategori', 'Lauk')->count(),
                'Sayur' => $query->where('kategori', 'Sayur')->count(),
                'Tambahan' => $query->where('kategori', 'Tambahan')->count(),
            ]
        ];
    }

    public function getUsageFrequency()
    {
        return $this->detailTransaksiDapur()->count();
    }

    public function canBeProducedInDapur($dapurId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if all required ingredients are available in the dapur's stock
        foreach ($this->bahanMenu as $bahan) {
            $stockItem = \App\Models\StockItem::where('id_dapur', $dapurId)
                ->where('id_template_item', $bahan->id_template_item)
                ->where('jumlah_stock', '>', 0)
                ->first();

            if (!$stockItem) {
                return false;
            }
        }

        return true;
    }

    public function checkStockAvailability(int $jumlahPorsi, int $dapurId): array
    {
        $requiredIngredients = $this->calculateRequiredIngredients($jumlahPorsi);
        $stockAvailability = [];
        $allAvailable = true;
        $totalCost = 0;

        foreach ($requiredIngredients as $ingredient) {
            $stockItem = \App\Models\StockItem::where('id_dapur', $dapurId)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $available = $stockItem ? $stockItem->jumlah_stock : 0;
            $needed = $ingredient['total_needed'];
            $isAvailable = $available >= $needed;

            if (!$isAvailable) {
                $allAvailable = false;
            }

            $stockAvailability[] = [
                'id_template_item' => $ingredient['id_template_item'],
                'nama_bahan' => $ingredient['nama_bahan'],
                'satuan' => $ingredient['satuan'],
                'needed' => $needed,
                'available' => $available,
                'is_available' => $isAvailable,
                'shortage' => $isAvailable ? 0 : ($needed - $available),
            ];
        }

        return [
            'menu_id' => $this->id_menu,
            'menu_name' => $this->nama_menu,
            'porsi' => $jumlahPorsi,
            'all_available' => $allAvailable,
            'ingredients' => $stockAvailability,
            'total_ingredients' => count($stockAvailability),
            'available_ingredients' => count(array_filter($stockAvailability, fn($item) => $item['is_available'])),
        ];
    }
}
