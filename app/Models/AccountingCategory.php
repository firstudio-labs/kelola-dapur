<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountingCategory extends Model
{
    use HasFactory;

    protected $table = 'accounting_categories';

    protected $fillable = [
        'id_dapur', 'name', 'type', 'group', 'is_tax', 'is_protected',
    ];

    protected $casts = [
        'is_tax' => 'boolean',
        'is_protected' => 'boolean',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function transactions()
    {
        return $this->hasMany(AccountingTransaction::class, 'category_id');
    }

    public function scopeForDapur($query, $dapurId)
    {
        return $query->where(function ($q) use ($dapurId) {
            $q->where('id_dapur', $dapurId)->orWhereNull('id_dapur');
        });
    }

    public function getGroupLabelAttribute(): string
    {
        return match ($this->group) {
            'dana_bahan_baku' => 'Dana Bahan Baku',
            'dana_operasional' => 'Dana Operasional',
            'dana_insentif_fasilitas' => 'Dana Insentif Fasilitas',
            'pungutan_ppn' => 'Pungutan/Setoran PPN',
            'pungutan_pph21' => 'Pungutan/Setoran PPh 21',
            'pungutan_pph22' => 'Pungutan/Setoran PPh 22',
            'pungutan_pph23' => 'Pungutan/Setoran PPh 23',
            'pungutan_pph4' => 'Pungutan/Setoran PPh pasal 4 ayat (2)',
            'biaya_bahan_baku' => 'Biaya Bahan Baku',
            'biaya_operasional' => 'Biaya Operasional',
            'biaya_insentif_fasilitas' => 'Biaya Insentif Fasilitas',
            default => $this->group,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'income' ? 'Penerimaan' : 'Pengeluaran';
    }
}
