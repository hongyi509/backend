<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalOrderStatus extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paypal_webhook_id' => 'integer',
        'full_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
