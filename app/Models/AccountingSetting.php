<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountingSetting extends Model
{
    use HasFactory;

    protected $table = 'accounting_settings';

    protected $fillable = [
        'id_dapur', 'institution_name', 'address', 'head_name', 'treasurer_name',
        'foundation_name', 'foundation_head', 'bank_account', 'report_location', 'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }
}
