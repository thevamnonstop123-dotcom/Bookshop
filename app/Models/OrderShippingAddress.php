<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'receiver_name',
        'phone_number',
        'address_line',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A shipping address belongs to an order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}