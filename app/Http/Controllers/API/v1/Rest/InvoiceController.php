<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoiceRequest;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function create(InvoiceRequest $request)
    {
        $item = Invoice::create($request->validated());
        return response()->json($item);
    }
}
