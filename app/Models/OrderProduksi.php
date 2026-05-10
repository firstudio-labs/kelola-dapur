<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduksi extends Model
{
    use HasFactory;

    protected $table = 'order_produksi';
    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_transaksi',
        'id_dapur',
        'status',
        'catatan',
        'ulasan',
        'ulasan_foto',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_STOK_KURANG   = 'stok_kurang';
    const STATUS_BELUM_DIBUAT  = 'belum_dibuat';
    const STATUS_SEDANG_DIBUAT = 'sedang_dibuat';
    const STATUS_SELESAI       = 'selesai';

    public static function statusLabel(): array
    {
        return [
            self::STATUS_STOK_KURANG   => 'Stok Kurang',
            self::STATUS_BELUM_DIBUAT  => 'Belum Dibuat',
            self::STATUS_SEDANG_DIBUAT => 'Sedang Dibuat',
            self::STATUS_SELESAI       => 'Selesai',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function isStokKurang(): bool
    {
        return $this->status === self::STATUS_STOK_KURANG;
    }

    public function transaksiDapur()
    {
        return $this->belongsTo(TransaksiDapur::class, 'id_transaksi', 'id_transaksi');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function dokumentasi()
    {
        return $this->hasMany(OrderProduksiDokumentasi::class, 'id_order', 'id_order');
    }

    public function distribusiOrder()
    {
        return $this->hasOne(OrderDistribusi::class, 'id_order', 'id_order');
    }
}
