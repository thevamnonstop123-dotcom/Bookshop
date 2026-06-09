<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'bio',
        'image',
        'status',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * An author has many books (many-to-many via book_author).
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }

    /**
     * The staff member who created this author.
     */
    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * The staff member who last updated this author.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}