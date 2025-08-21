<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanMenu extends Model
{
    use HasFactory;

    protected $table = 'bahan_menu';
    protected $primaryKey = 'id_bahan_menu';

    protected $fillable = [
        'id_menu',
        'id_template_item',
        'jumlah_per_porsi',
        'is_bahan_basah'
    ];

    protected $casts = [
        'jumlah_per_porsi' => 'decimal:4',
        'is_bahan_basah' => 'boolean',
    ];

    public function menuMakanan()
    {
        return $this->belongsTo(MenuMakanan::class, 'id_menu');
    }

    public function templateItem()
    {
        return $this->belongsTo(TemplateItem::class, 'id_template_item');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    /**
     * @return float
     */
    public function getBeratBasah(): float
    {
        if (!$this->is_bahan_basah) {
            return (float) $this->jumlah_per_porsi;
        }

        return (float) $this->jumlah_per_porsi * 1.07;
    }

    /** 
     * @param int
     * @return float
     */
    public function getTotalBeratBasah(int $porsi): float
    {
        return $this->getBeratBasah() * $porsi;
    }

    /**
     * @return string
     */
    public function getFormattedBeratBasah(): string
    {
        $beratBasah = $this->getBeratBasah();
        $satuan = $this->templateItem->satuan ?? '';

        return number_format($beratBasah, 2) . ' ' . $satuan;
    }
}
