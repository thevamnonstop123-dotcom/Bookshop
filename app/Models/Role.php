<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'can_manage_books',
        'can_manage_orders',
        'can_manage_users',
        'can_view_reports',
    ];

    protected $casts = [
        'can_manage_books' => 'boolean',
        'can_manage_orders' => 'boolean',
        'can_manage_users' => 'boolean',
        'can_view_reports' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A role has many staff members.
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}