<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraDapur extends Model
{
    protected $table = 'mitra_dapur';

    protected $fillable = [
        'id_mitra',
        'id_dapur',
        'status',
        'catatan',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default    => 'bg-secondary',
        };
    }
}
