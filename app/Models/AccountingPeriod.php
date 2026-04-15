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

    public function scopeLatestClosed($query, $dapurId)
    {
        return $query->where('id_dapur', $dapurId)
                     ->where('status', 'closed')
                     ->orderByDesc('end_date');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Get the most recent closed period before this one for the same dapur.
     */
    public function getPreviousPeriod(): ?self
    {
        return static::where('id_dapur', $this->id_dapur)
                     ->where('status', 'closed')
                     ->where('end_date', '<', $this->start_date)
                     ->orderByDesc('end_date')
                     ->first();
    }

    /**
     * Compute closing balance for each cash account in this period and persist it.
     * closing_balance = opening_balance + total_debit - total_credit
     *
     * Returns array of [cash_account_id => closing_balance]
     */
    public function computeAndSaveClosingBalances(): array
    {
        $results = [];

        $balances = $this->balances()->get();

        foreach ($balances as $balance) {
            $totalDebit  = $this->transactions()
                                ->where('cash_account_id', $balance->cash_account_id)
                                ->sum('debit');
            $totalCredit = $this->transactions()
                                ->where('cash_account_id', $balance->cash_account_id)
                                ->sum('credit');

            $closing = (float) $balance->opening_balance + (float) $totalDebit - (float) $totalCredit;

            $balance->update(['closing_balance' => $closing]);
            $results[$balance->cash_account_id] = $closing;
        }

        return $results;
    }
}
