<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhliGizi extends Model
{
    use HasFactory;

    protected $table = 'ahli_gizi';
    protected $primaryKey = 'id_ahli_gizi';

    protected $fillable = ['id_user_role', 'id_dapur'];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'ahli_gizi');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserRole::class,
            'id_user_role', // Foreign key on ahli_gizi table
            'id_user', // Foreign key on user_roles table
            'id_user_role', // Local key on ahli_gizi table
            'id_user' // Local key on user_roles table
        );
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function approvalTransaksi()
    {
        return $this->hasMany(ApprovalTransaksi::class, 'id_ahli_gizi');
    }

    public function transaksiDapur()
    {
        return $this->hasMany(TransaksiDapur::class, 'created_by', 'id_user');
    }
}
