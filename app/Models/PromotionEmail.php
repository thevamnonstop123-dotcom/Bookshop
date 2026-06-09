<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionEmail extends Model
{
    protected $fillable = [
        'subject', 'message', 'sent_by', 'recipients_count', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function sentBy()
    {
        return $this->belongsTo(Staff::class, 'sent_by');
    }
}