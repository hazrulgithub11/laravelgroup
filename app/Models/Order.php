<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'provider_id', 
        'washing',
        'ironing',
        'dry_cleaning',
        'extra_load_small',
        'extra_load_large',
        'total', 
        'status',
        'address',
        'latitude',
        'longitude',
        'pickup_time',
        'delivery_time',
        'delivery_charge',
    ];

    protected $casts = [
        'washing' => 'boolean',
        'ironing' => 'boolean',
        'dry_cleaning' => 'boolean',
        'extra_load_small' => 'integer',
        'extra_load_large' => 'integer',
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
        ][$this->status] ?? 'secondary';
    }
} 