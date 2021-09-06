<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalWebhook extends Model
{
    protected $guarded = [];

    protected $casts = [
        'full_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

}
