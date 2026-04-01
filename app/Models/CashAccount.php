<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashAccount extends Model
{
    use HasFactory;

    protected $table = 'cash_accounts';

    protected $fillable = [
        'id_dapur', 'name', 'type',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function transactions()
    {
        return $this->hasMany(AccountingTransaction::class, 'cash_account_id');
    }

    public function scopeForDapur($query, $dapurId)
    {
        return $query->where('id_dapur', $dapurId);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'tunai' ? 'Kas Tunai' : 'Kas Bank';
    }
}
