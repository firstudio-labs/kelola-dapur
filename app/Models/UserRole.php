<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';
    protected $primaryKey = 'id_user_role';

    protected $fillable = ['id_user', 'role_type', 'id_dapur'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class, 'id_user_role');
    }

    public function kepalaDapur()
    {
        return $this->hasOne(KepalaDapur::class, 'id_user_role');
    }

    public function ahliGizi()
    {
        return $this->hasOne(AhliGizi::class, 'id_user_role');
    }

    public function adminGudang()
    {
        return $this->hasOne(AdminGudang::class, 'id_user_role');
    }
}
