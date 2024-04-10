<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['user_id', 'transaction_id', 'amount', 'details'];

    protected $attributes = ['status'=>'pending'];

    public function setDetailsAttribute($value) :void
    {
        $this->attributes['details'] = json_encode($value);
    }

    public function getDetailsAttribute($value)
    {
        return json_decode($value, true);
    }
}
