<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
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
     * A category has many books.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * The staff member who created this category.
     */
    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * The staff member who last updated this category.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}