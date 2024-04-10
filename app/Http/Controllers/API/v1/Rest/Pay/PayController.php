<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Actions\InvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pay\PayRequest;
use Illuminate\Http\Request;

class PayController extends Controller
{
    public array $headers;
    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token',
            'Host' => 'partner.atmos.uz',
        ];
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'details' => 'array|required',
        ]);
        $invoiceId = (new InvoiceAction)->createInvoice($request->only('user_id', 'amount', 'details'));
        $postData = [
            'account' => $invoiceId,
            'amount' => $request->amount,
            'store_id' => 7954,
        ];

        $result = \Http::withHeaders($this->headers)
            ->post('https://partner.atmos.uz/merchant/pay/create', $postData)->body();
        return response()->json([
            'success' => true,
            'data' => ['transaction_id' => $result['transaction_id']]
        ]);

    }
    public function preApplyTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'card_number' => 'required',
            'expiry' => 'required',
        ]);
        $postData = $request->only('transaction_id', 'card_number', 'expiry');
        $postData['store_id'] = 7954;
        $result = \Http::withHeaders($this->headers)->post('https://partner.atmos.uz/merchant/pay/pre-apply', $postData)->body();
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function applyTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'otp' => 'required',
        ]);
        $postData = [
            'otp' => $request->otp,
            'transaction_id' => $request->transaction_id,
            'store_id' => 7954,
        ];
        $result = \Http::withHeaders($this->headers)->post('https://partner.atmos.uz/merchant/pay/apply', $postData)->body();
        return response()->json([
            'success' => true,
            'data' => []
        ]);

    }
}
