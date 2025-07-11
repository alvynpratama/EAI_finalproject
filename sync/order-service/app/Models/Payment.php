<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'payment_mysql';
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date'
    ];
}
