<?php

namespace App\Http\Requests\Pay;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required',
            'amount' => 'required',
            'details' => 'array|required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
