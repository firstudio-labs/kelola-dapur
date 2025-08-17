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
        'jumlah_per_porsi'
    ];

    protected $casts = [
        'jumlah_per_porsi' => 'decimal:4',
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
}
