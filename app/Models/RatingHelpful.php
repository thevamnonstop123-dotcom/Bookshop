<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingHelpful extends Model
{
    protected $fillable = ['rating_id', 'customer_id'];

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }
}