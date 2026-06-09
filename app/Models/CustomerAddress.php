<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'receiver_name',
        'phone_number',
        'address_line',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * An address belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}