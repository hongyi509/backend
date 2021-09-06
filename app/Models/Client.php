<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Client extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($client) {
            $client->key = implode('', Arr::random(Arr::shuffle([1,2,3,4,5,6,7,8,9]), 5));
        });
    }
}
