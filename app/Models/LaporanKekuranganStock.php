<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKekuranganStock extends Model
{
    use HasFactory;

    protected $table = 'laporan_kekurangan_stock';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_transaksi',
        'id_template_item',
        'jumlah_dibutuhkan',
        'jumlah_tersedia',
        'jumlah_kurang',
        'satuan',
        'status'
    ];

    protected $casts = [
        'jumlah_dibutuhkan' => 'decimal:3',
        'jumlah_tersedia' => 'decimal:3',
        'jumlah_kurang' => 'decimal:3',
    ];

    // Relationships
    public function transaksiDapur()
    {
        return $this->belongsTo(TransaksiDapur::class, 'id_transaksi');
    }


    public function templateItem()
    {
        return $this->belongsTo(TemplateItem::class, 'id_template_item');
    }

    // Helper methods
    public static function createFromShortage(int $transaksiId, array $shortageData): self
    {
        return self::create([
            'id_transaksi' => $transaksiId,
            'id_template_item' => $shortageData['id_template_item'],
            'jumlah_dibutuhkan' => $shortageData['needed'],
            'jumlah_tersedia' => $shortageData['available'],
            'jumlah_kurang' => $shortageData['shortage'],
            'satuan' => $shortageData['satuan'],
            'status' => 'pending'
        ]);
    }

    public function resolve(): bool
    {
        $this->status = 'resolved';
        return $this->save();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-label-warning',
            'resolved' => 'bg-label-success',
            default => 'bg-label-secondary'
        };
    }

    public function getFormattedJumlahKurang(): string
    {
        return number_format($this->jumlah_kurang, 2) . ' ' . $this->satuan;
    }

    public function getFormattedJumlahDibutuhkan(): string
    {
        return number_format($this->jumlah_dibutuhkan, 2) . ' ' . $this->satuan;
    }

    public function getFormattedJumlahTersedia(): string
    {
        return number_format($this->jumlah_tersedia, 2) . ' ' . $this->satuan;
    }
}
