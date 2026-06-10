<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookAccess extends Model
{
        protected $fillable = [
        'customer_id',
        'book_id',
        'order_id',
        'device_token',
        'last_accessed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}