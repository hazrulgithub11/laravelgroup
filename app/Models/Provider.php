<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Provider extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'telegram_username',
        'address',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
} 