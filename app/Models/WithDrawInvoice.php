<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithDrawInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];

    protected $attributes = ['status'=>'pending'];
}
