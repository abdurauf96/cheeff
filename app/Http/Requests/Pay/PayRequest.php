<?php

namespace App\Http\Requests\Pay;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function rules()
    {
        return [
            'transaction_id' => 'required',
            'card_number' => 'required',
            'expiry' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
