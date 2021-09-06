<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'full_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

}
