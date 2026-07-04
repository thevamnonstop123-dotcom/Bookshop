<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'customer_id',
        'recipient_type',
        'recipient_id',
        'type',
        'title',
        'message',
        'notifiable_type',
        'notifiable_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->notifiable_type || !$this->notifiable_id) {
            return null;
        }

        $type = ltrim($this->notifiable_type, '\\');
        
        // For staff recipients, use admin routes
        if ($this->recipient_type === 'App\\Models\\Staff' || $this->recipient_type === 'App\Models\Staff') {
            return match ($type) {
                'App\Models\Order' => route('admin.orders.show', $this->notifiable_id),
                'App\Models\Book' => route('admin.books.edit', $this->notifiable_id),
                'App\Models\Rating' => route('admin.reviews.index'),
                default => null,
            };
        }
        
        // For customer recipients, use customer routes
        return match ($type) {
            'App\Models\Order' => route('customer.orders.show', $this->notifiable_id),
            default => null,
        };
    }
}
