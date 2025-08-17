<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KepalaDapur extends Model
{
    use HasFactory;

    protected $table = 'kepala_dapur';
    protected $primaryKey = 'id_kepala_dapur';

    protected $fillable = ['id_user_role', 'id_dapur'];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'kepala_dapur');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserRole::class,
            'id_user_role', // Foreign key on kepala_dapur table
            'id_user', // Foreign key on user_roles table
            'id_user_role', // Local key on kepala_dapur table
            'id_user' // Local key on user_roles table
        );
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function approvalStockItems(): HasMany
    {
        return $this->hasMany(ApprovalStockItem::class, 'id_kepala_dapur');
    }
}
