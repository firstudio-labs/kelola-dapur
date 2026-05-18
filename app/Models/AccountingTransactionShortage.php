<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTransactionShortage extends Model
{
    use HasFactory;

    protected $table = 'accounting_transaction_shortages';

    protected $fillable = [
        'accounting_transaction_id',
        'laporan_kekurangan_id',
        'harga_satuan',
        'qty_dibeli',
        'nominal'
    ];

    protected $casts = [
        'qty_dibeli' => 'decimal:3',
        'nominal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(AccountingTransaction::class, 'accounting_transaction_id');
    }

    public function laporanKekurangan()
    {
        return $this->belongsTo(LaporanKekuranganStock::class, 'laporan_kekurangan_id', 'id_laporan');
    }
}
