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

    public function hasGambar(): bool
    {
        return !empty($this->gambar_menu) && $this->isGambarExists();
    }

    public function isGambarExists(): bool
    {
        if (!$this->gambar_menu) {
            return false;
        }

        return Storage::disk('public')->exists('menu/' . $this->gambar_menu) ||
            file_exists(public_path('images/menu/' . $this->gambar_menu));
    }

    public function deleteGambar(): bool
    {
        if (!$this->gambar_menu) {
            return true;
        }

        $deleted = false;

        if (Storage::disk('public')->exists('menu/' . $this->gambar_menu)) {
            $deleted = Storage::disk('public')->delete('menu/' . $this->gambar_menu);
        }

        $publicPath = public_path('images/menu/' . $this->gambar_menu);
        if (file_exists($publicPath)) {
            $deleted = unlink($publicPath);
        }

        return $deleted;
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

    // Helper methods 
    public function calculateRequiredIngredients(int $porsi): array
    {
        $ingredients = [];

        foreach ($this->bahanMenu as $bahan) {
            $jumlahPerPorsi = $bahan->getBeratBasah();

            $ingredients[] = [
                'id_template_item' => $bahan->id_template_item,
                'nama_bahan' => $bahan->templateItem->nama_bahan,
                'satuan' => $bahan->templateItem->satuan,
                'jumlah_per_porsi' => $bahan->jumlah_per_porsi,
                'is_bahan_basah' => $bahan->is_bahan_basah,
                'berat_basah_per_porsi' => $jumlahPerPorsi,
                'total_needed' => $jumlahPerPorsi * $porsi,
                'template_item' => $bahan->templateItem
            ];
        }

        return $ingredients;
    }

    public function checkStockAvailability(int $porsi, int $dapurId): array
    {
        $result = [
            'can_produce' => true,
            'shortage' => [],
            'ingredients' => []
        ];

        $requiredIngredients = $this->calculateRequiredIngredients($porsi);

        foreach ($requiredIngredients as $ingredient) {
            $stockItem = StockItem::where('id_dapur', $dapurId)
                ->where('id_template_item', $ingredient['id_template_item'])
                ->first();

            $available = $stockItem ? (float)$stockItem->jumlah : 0;
            $needed = $ingredient['total_needed'];

            $ingredientData = [
                'id_template_item' => $ingredient['id_template_item'],
                'nama_bahan' => $ingredient['nama_bahan'],
                'satuan' => $ingredient['satuan'],
                'needed' => $needed,
                'available' => $available,
                'sufficient' => $available >= $needed,
                'is_bahan_basah' => $ingredient['is_bahan_basah'],
                'berat_asli' => $ingredient['jumlah_per_porsi'] * $porsi,
                'berat_basah' => $ingredient['total_needed']
            ];

            if ($available < $needed) {
                $result['can_produce'] = false;
                $result['shortage'][] = [
                    'id_template_item' => $ingredient['id_template_item'],
                    'nama_bahan' => $ingredient['nama_bahan'],
                    'satuan' => $ingredient['satuan'],
                    'needed' => $needed,
                    'available' => $available,
                    'shortage' => $needed - $available,
                    'is_bahan_basah' => $ingredient['is_bahan_basah']
                ];
            }

            $result['ingredients'][] = $ingredientData;
        }

        return $result;
    }
    public function getTotalProductionCount(): int
    {
        return $this->detailTransaksiDapur()
            ->whereHas('transaksiDapur', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('jumlah_porsi');
    }

    // Scope query
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeWithGambar($query)
    {
        return $query->whereNotNull('gambar_menu');
    }

    public function scopeWithoutGambar($query)
    {
        return $query->whereNull('gambar_menu');
    }
}
