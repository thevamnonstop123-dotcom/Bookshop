<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'book_id',
        'quantity',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A cart item belongs to a cart.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * A cart item references a book.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the subtotal for this cart item.
     */
    public function subtotal(): float
    {
        return $this->quantity * $this->book->price;
    }
}