<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A cart belongs to one customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * A cart has many cart items.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}