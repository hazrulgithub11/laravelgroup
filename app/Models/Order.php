<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'total',
        'status',
        'address',
        'latitude',
        'longitude',
        'pickup_time',
        'delivery_time',
        'delivery_charge',
        'telegram_username'
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
    ];

    protected $with = ['provider'];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getTotalAttribute($value)
    {
        // Get categories from provider
        $categories = $this->provider->categories;
        
        // Calculate category count based on whether it's already an array or JSON string
        $categoryCount = is_array($categories) ? count($categories) : count(json_decode($categories, true));
        
        // Calculate total (RM10 per category + delivery charge)
        return ($categoryCount * 10) + $this->delivery_charge;
    }
} 