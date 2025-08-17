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
            'id_user_role', // Foreign key on super_admin table
            'id_user', // Foreign key on user_roles table
            'id_user_role', // Local key on super_admin table
            'id_user' // Local key on user_roles table
        );
    }
}
