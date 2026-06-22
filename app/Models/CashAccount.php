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

    public function balances()
    {
        return $this->hasMany(AccountingBalance::class, 'cash_account_id');
    }

    public function getBalanceForPeriod($periodId)
    {
        return $this->balances()->where('period_id', $periodId)->first();
    }

    public function getCurrentBalanceAttribute(): float
    {
        $activePeriod = AccountingPeriod::forDapur($this->id_dapur)->where('status', 'open')->first() 
                      ?? AccountingPeriod::forDapur($this->id_dapur)->orderByDesc('end_date')->first();
        
        if (!$activePeriod) return 0;

        $openingBalance = $this->getBalanceForPeriod($activePeriod->id)->opening_balance ?? 0;
        
        $totalDebit = $this->transactions()->where('period_id', $activePeriod->id)->sum('debit');
        $totalCredit = $this->transactions()->where('period_id', $activePeriod->id)->sum('credit');

        return (float) ($openingBalance + $totalDebit - $totalCredit);
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
