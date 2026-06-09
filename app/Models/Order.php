<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * An order belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * An order has many order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * An order has one shipping address (snapshot).
     */
    public function shippingAddress()
    {
        return $this->hasOne(OrderShippingAddress::class);
    }

    /**
     * An order has one payment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment?->status === 'completed';
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}