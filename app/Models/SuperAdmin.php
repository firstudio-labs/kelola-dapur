<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'super_admin';
    protected $primaryKey = 'id_super_admin';

    protected $fillable = ['id_user_role'];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'super_admin');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserRole::class,
            'id_user_role', 
            'id_user', 
            'id_user_role', 
            'id_user' 
        );
    }
}
