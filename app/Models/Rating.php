<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'customer_id', 'book_id', 'rating', 'review',
        'helpful_count', 'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function helpfuls()
    {
        return $this->hasMany(RatingHelpful::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRating($query, int $stars)
    {
        return $query->where('rating', $stars);
    }

    // Helpers
    public function isHelpfulBy(int $customerId): bool
    {
        return $this->helpfuls()->where('customer_id', $customerId)->exists();
    }
}