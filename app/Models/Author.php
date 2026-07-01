<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'bio', 'image', 'country_id', 'website',
        'joined_date', 'sales_count',
        'status', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'sales_count' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'author_genre');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    // ========== ACCESSORS ==========

    public function getAvatarUrlAttribute(): string
    {
        if ($this->image && $this->image !== 'default.png') {
            return asset('storage/' . $this->image);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1E3A8A&color=fff&size=120';
    }

    public function getActiveYearsAttribute(): string
    {
        if (!$this->joined_date) return '';
        $start = $this->joined_date->format('Y');
        $now = now()->format('Y');
        return $start === $now ? $start : $start . ' — ' . $now;
    }

    public function getPopularityLabelAttribute(): string
    {
        if ($this->sales_count > 10000) return 'Bestseller';
        if ($this->sales_count > 5000) return 'Popular';
        if ($this->sales_count > 1000) return 'Rising Star';
        return '';
    }
}