<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalTransaksi extends Model
{
    use HasFactory;

    protected $table = 'approval_transaksi';
    protected $primaryKey = 'id_approval_transaksi';

    protected $fillable = [
        'id_transaksi',
        'id_ahli_gizi',
        'id_kepala_dapur',
        'status',
        'keterangan',
        'catatan_approval',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function transaksiDapur()
    {
        return $this->belongsTo(TransaksiDapur::class, 'id_transaksi');
    }

    public function ahliGizi()
    {
        return $this->belongsTo(AhliGizi::class, 'id_ahli_gizi');
    }

    public function kepalaDapur()
    {
        return $this->belongsTo(KepalaDapur::class, 'id_kepala_dapur');
    }

    // Helper methods
    public function approve(string $catatan = null): bool
    {
        $this->status = 'approved';
        $this->catatan_approval = $catatan;
        $this->approved_at = now();

        if ($this->save()) {
            // Process transaksi setelah approve
            $result = $this->transaksiDapur->processTransaction();
            return $result['success'];
        }

        return false;
    }

    public function reject(string $catatan): bool
    {
        $this->status = 'rejected';
        $this->catatan_approval = $catatan;
        $this->approved_at = now();

        if ($this->save()) {
            $this->transaksiDapur->status = 'cancelled';
            return $this->transaksiDapur->save();
        }

        return false;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-label-warning',
            'approved' => 'bg-label-success',
            'rejected' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }
}
