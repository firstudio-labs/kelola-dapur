<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDistribusiDetail extends Model
{
    use HasFactory;

    protected $table = 'order_distribusi_details';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_distribusi',
        'id_penerima',
        'porsi_besar',
        'porsi_kecil',
        'jumlah_diterima',
        'status',
        'catatan',
        'status_penerimaan',
        'ulasan',
        'porsi_besar_diterima',
        'porsi_kecil_diterima',
    ];

    const STATUS_BELUM_DIKIRIM  = 'belum_dikirim';
    const STATUS_SEDANG_DIKIRIM = 'sedang_dikirim';
    const STATUS_SUDAH_DIKIRIM  = 'sudah_dikirim';

    const STATUS_PENERIMAAN_MENUNGGU = 'menunggu';
    const STATUS_PENERIMAAN_DITERIMA = 'diterima';
    const STATUS_PENERIMAAN_DITOLAK  = 'ditolak';

    public static function statusPenerimaanLabel(): array
    {
        return [
            self::STATUS_PENERIMAAN_MENUNGGU => 'Menunggu Konfirmasi',
            self::STATUS_PENERIMAAN_DITERIMA => 'Diterima',
            self::STATUS_PENERIMAAN_DITOLAK  => 'Tidak Diterima',
        ];
    }

    public function getStatusPenerimaanLabelAttribute(): string
    {
        return self::statusPenerimaanLabel()[$this->status_penerimaan] ?? $this->status_penerimaan;
    }

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

    public function orderDistribusi()
    {
        return $this->belongsTo(OrderDistribusi::class, 'id_distribusi', 'id_distribusi');
    }

    public function penerimaMbg()
    {
        return $this->belongsTo(PenerimaMbg::class, 'id_penerima', 'id_penerima');
    }

    public function dokumentasi()
    {
        return $this->hasMany(OrderDistribusiDetailDokumentasi::class, 'id_detail', 'id_detail');
    }

    public function penerimaanFoto()
    {
        return $this->hasMany(OrderDistribusiDetailPenerimaanFoto::class, 'id_detail', 'id_detail');
    }
}
