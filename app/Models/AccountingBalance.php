<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountingBalance extends Model
{
    use HasFactory;

    protected $table = 'accounting_balances';

    protected $fillable = [
        'period_id', 'opening_balance', 'closing_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }
}
