<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountingPeriod extends Model
{
    use HasFactory;

    protected $table = 'accounting_periods';

    protected $fillable = [
        'id_dapur', 'name', 'year', 'start_date', 'end_date', 'next_period_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_period_date' => 'date',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function transactions()
    {
        return $this->hasMany(AccountingTransaction::class, 'period_id');
    }

    public function balances()
    {
        return $this->hasMany(AccountingBalance::class, 'period_id');
    }

    /**
     * Get the balance for a specific cash account in this period.
     */
    public function getBalanceForAccount($cashAccountId)
    {
        return $this->balances()->where('cash_account_id', $cashAccountId)->first();
    }

    public function scopeForDapur($query, $dapurId)
    {
        return $query->where('id_dapur', $dapurId);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
