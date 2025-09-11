<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // app/Models/Order.php
protected $fillable = [
    'user_id',
    'total_amount', 
    'status',
    'shipping_address',
    'phone'
];

public function user() {
    return $this->belongsTo(User::class);
}
}
