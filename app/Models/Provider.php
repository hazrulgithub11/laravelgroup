<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'telegram_username',
        'telegram_chat_id',
        'address',
        'latitude',
        'longitude',
        'service',
        'categories',
        'profile_picture',
        'introduction',
        'years_experience',
        'payment_methods'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'categories' => 'array',
        'payment_methods' => 'array'
    ];

    // Specify that id is auto-incrementing
    public $incrementing = true;
    protected $keyType = 'int';

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}