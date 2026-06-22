<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiHandlerBahan extends Model
{
    use HasFactory;

    protected $table = 'produksi_handler_bahan';
    protected $primaryKey = 'id_handler';

    protected $fillable = [
        'id_order',
        'id_template_item',
        'jenis',
        'jumlah',
        'catatan',
        'status',
    ];

    public function orderProduksi()
    {
        return $this->belongsTo(OrderProduksi::class, 'id_order', 'id_order');
    }

    public function templateItem()
    {
        return $this->belongsTo(TemplateItem::class, 'id_template_item', 'id_template_item');
    }

    public function laporanKekuranganStock()
    {
        return $this->hasOne(LaporanKekuranganStock::class, 'id_handler', 'id_handler');
    }
}
