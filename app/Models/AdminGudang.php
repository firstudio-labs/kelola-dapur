<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminGudang extends Model
{
    use HasFactory;

    protected $table = 'admin_gudang';
    protected $primaryKey = 'id_admin_gudang';

    protected $fillable = ['id_user_role', 'id_dapur'];

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'id_user_role')->where('role_type', 'admin_gudang');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            UserRole::class,
            'id_user_role', // Foreign key on admin_gudang table
            'id_user', // Foreign key on user_roles table
            'id_user_role', // Local key on admin_gudang table
            'id_user' // Local key on user_roles table
        );
    }

    public function dapur()
    {
        return $this->belongsTo(Dapur::class, 'id_dapur');
    }

    public function approvalStockItems(): HasMany
    {
        return $this->hasMany(ApprovalStockItem::class, 'id_admin_gudang');
    }
}
