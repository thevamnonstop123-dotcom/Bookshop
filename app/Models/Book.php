<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'isbn',
        'price',
        'stock_quantity',
        'language',
        'published_date',
        'description',
        'image',
        'status',
        'is_ebook',
        'ebook_file',
        'created_by',
        'updated_by',
        'category_id', 'title', 'slug', 'isbn', 'price', 'sale_price',
        'sale_starts_at', 'sale_ends_at', 'stock_quantity', 'language',
        'published_date', 'description', 'image', 'status',
        'created_by', 'updated_by',
    ];

    protected $casts = [
    'price' => 'decimal:2',
    'sale_price' => 'decimal:2',
    'published_date' => 'date',
    'sale_starts_at' => 'datetime',
    'sale_ends_at' => 'datetime',
    'is_ebook' => 'boolean',
];

// Add this helper method
public function isOnSale(): bool
{
    if (!$this->sale_price) {
        return false;
    }

    $now = now();

    if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
        return false;
    }

    if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
        return false;
    }

    return true;
}

public function discountPercentage(): int
{
    if (!$this->isOnSale()) {
        return 0;
    }

    return round((($this->price - $this->sale_price) / $this->price) * 100);
}
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * A book belongs to one category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A book has many authors (many-to-many via book_author).
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    /**
     * The staff member who created this book.
     */
    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * The staff member who last updated this book.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    /**
     * A book appears in many cart items.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * A book appears in many order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if book is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if book is active and visible to customers.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

     /**
     * Check if book is an e-book.
     */
    public function isEbook(): bool
    {
        return (bool) $this->is_ebook;
    }

    // In the relationships section:
    public function ratings()
    {
        return $this->hasMany(Rating::class)->where('status', 'active');
    }

    // Helper for rating distribution
    public function ratingDistribution(): array
    {
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $this->ratings()->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $this->rating_count > 0
                    ? round(($count / $this->rating_count) * 100)
                    : 0,
            ];
        }
        return $distribution;
    }
}