<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'id_user');
    }

    public function accessibleDapur()
    {
        return $this->hasManyThrough(
            Dapur::class,
            UserRole::class,
            'id_user',
            'id_dapur',
            'id_user',
            'id_dapur'
        )->whereNotNull('user_roles.id_dapur');
    }

    // Helper methods
    public function isSuperAdmin(): bool
    {
        return $this->userRole && $this->userRole->role_type === 'super_admin';
    }

    public function isKepalaDapur(?int $dapurId = null): bool
    {
        if (!$this->userRole || $this->userRole->role_type !== 'kepala_dapur') {
            return false;
        }
        if ($dapurId) {
            return $this->userRole->id_dapur === $dapurId;
        }
        return true;
    }

    public function isAdminGudang(?int $dapurId = null): bool
    {
        if (!$this->userRole || $this->userRole->role_type !== 'admin_gudang') {
            return false;
        }
        if ($dapurId) {
            return $this->userRole->id_dapur === $dapurId;
        }
        return true;
    }

    public function isAhliGizi(?int $dapurId = null): bool
    {
        if (!$this->userRole || $this->userRole->role_type !== 'ahli_gizi') {
            return false;
        }
        if ($dapurId) {
            return $this->userRole->id_dapur === $dapurId;
        }
        return true;
    }

    public function getUserRole(?int $dapurId = null): string
    {
        if (!$this->userRole) {
            return 'no_role';
        }
        if ($dapurId && $this->userRole->id_dapur !== $dapurId) {
            return 'no_role';
        }
        return $this->userRole->role_type;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        // Jika ada context dapur dari middleware
        if (request()->route('dapur')) {
            $dapur = request()->route('dapur');

            return $this->whereHas('userRole', function ($query) use ($dapur) {
                $query->where('id_dapur', $dapur->id_dapur)
                    ->whereIn('role_type', ['admin_gudang', 'ahli_gizi']);
            })->where($field ?? $this->getRouteKeyName(), $value)->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }
    public function getRouteKeyName()
    {
        return 'id_user';
    }
}
