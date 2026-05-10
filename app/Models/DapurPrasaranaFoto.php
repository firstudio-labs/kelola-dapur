<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DapurPrasaranaFoto extends Model
{
    use HasFactory;

    protected $table = 'dapur_prasarana_foto';
    protected $primaryKey = 'id_foto';

    protected $fillable = [
        'id_dapur_prasarana',
        'foto_url',
    ];

    public function dapurPrasarana()
    {
        return $this->belongsTo(DapurPrasarana::class, 'id_dapur_prasarana', 'id_dapur_prasarana');
    }
}
