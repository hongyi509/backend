<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'last_seen_at' => 'datetime',
        'passport_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
