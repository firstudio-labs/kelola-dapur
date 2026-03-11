<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DapurPrasarana extends Model
{
    use HasFactory;

    protected $table = 'dapur_prasarana';
    protected $primaryKey = 'id_dapur_prasarana';

    protected $fillable = [
        'id_dapur',
        'id_item',
        'is_available',
    ];

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur', 'id_dapur');
    }

    public function item()
    {
        return $this->belongsTo(ItemPrasarana::class, 'id_item', 'id_item');
    }
}
