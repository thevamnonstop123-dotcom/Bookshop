<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['name'];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_genre');
    }
}