<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'dob',
        'password',
        'image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'dob' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A staff member belongs to one role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if staff has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role?->$permission ?? false;
    }

    public function canManageBooks(): bool
    {
        return $this->hasPermission('can_manage_books');
    }

    public function canManageOrders(): bool
    {
        return $this->hasPermission('can_manage_orders');
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermission('can_manage_users');
    }

    public function canViewReports(): bool
    {
        return $this->hasPermission('can_view_reports');
    }
}