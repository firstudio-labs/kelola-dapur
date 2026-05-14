<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDistribusi extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi';
    protected $primaryKey = 'id_distribusi';

    protected $fillable = [
        'id_order',
        'id_dapur',
        'status',
        'catatan',
        'ulasan',
        'ulasan_foto',
        'tanggal_dikirim',
    ];

    protected $casts = [
        'tanggal_dikirim' => 'datetime',
    ];

    const STATUS_BELUM_DIKIRIM  = 'belum_dikirim';
    const STATUS_SEDANG_DIKIRIM = 'sedang_dikirim';
    const STATUS_SUDAH_DIKIRIM  = 'sudah_dikirim';

    public static function statusLabel(): array
    {
        return [
            self::STATUS_BELUM_DIKIRIM  => 'Belum Dikirim',
            self::STATUS_SEDANG_DIKIRIM => 'Sedang Dikirim',
            self::STATUS_SUDAH_DIKIRIM  => 'Sudah Dikirim',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function orderProduksi()
    {
        return $this->belongsTo(OrderProduksi::class, 'id_order', 'id_order');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function dokumentasi()
    {
        return $this->hasMany(OrderDistribusiDokumentasi::class, 'id_distribusi', 'id_distribusi');
    }

    public function details()
    {
        return $this->hasMany(OrderDistribusiDetail::class, 'id_distribusi', 'id_distribusi');
    }

    public function recalculateStatus(): void
    {
        $details = $this->details()->get();

        if ($details->isEmpty()) {
            return;
        }

        $allDone    = $details->every(fn($d) => $d->status === self::STATUS_SUDAH_DIKIRIM);
        $anyStarted = $details->contains(fn($d) => in_array($d->status, [self::STATUS_SEDANG_DIKIRIM, self::STATUS_SUDAH_DIKIRIM]));

        $this->status = $allDone ? self::STATUS_SUDAH_DIKIRIM
            : ($anyStarted ? self::STATUS_SEDANG_DIKIRIM : self::STATUS_BELUM_DIKIRIM);

        if ($this->status === self::STATUS_SUDAH_DIKIRIM && !$this->tanggal_dikirim) {
            $this->tanggal_dikirim = now();
        }

        $this->save();
    }
}
