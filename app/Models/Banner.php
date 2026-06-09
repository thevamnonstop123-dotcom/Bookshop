<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
        'display_order',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The staff member who created this banner.
     */
    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * The staff member who last updated this banner.
     */
    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}