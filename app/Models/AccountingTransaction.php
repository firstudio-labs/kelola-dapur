<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountingTransaction extends Model
{
    use HasFactory;

    protected $table = 'accounting_transactions';

    protected $fillable = [
        'period_id', 'date', 'month', 'no_bukti', 'description',
        'debit', 'credit', 'category_id', 'cash_account_id', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    public function category()
    {
        return $this->belongsTo(AccountingCategory::class, 'category_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function scopeForPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    public function scopeForDapur($query, $dapurId)
    {
        return $query->whereHas('period', function ($q) use ($dapurId) {
            $q->where('id_dapur', $dapurId);
        });
    }

    public function scopeOrderedByDate($query)
    {
        return $query->orderBy('date')->orderBy('id');
    }

    public function shortages()
    {
        return $this->hasMany(AccountingTransactionShortage::class, 'accounting_transaction_id');
    }
}
