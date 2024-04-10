<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Actions\InvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pay\InvoiceRequest;
use App\Http\Requests\Pay\PayRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PayController extends Controller
{
    public array $headers;
    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.request()->header('Authorization'),
            'Host' => 'partner.atmos.uz',
        ];
    }

    public function createTransaction(InvoiceRequest $request)
    {
        $postData = [
            'account' => $request->user_id,
            'amount' => $request->amount,
            'store_id' => 7954,
        ];
        try {

            $response = \Http::withHeaders($this->headers)
                ->post('https://partner.atmos.uz/merchant/pay/create', $postData)->body();
            $invoiceData = $request->validated();
            $invoiceData['transaction_id'] = $response['transaction_id'];
            (new InvoiceAction)->createInvoice($invoiceData);
            return response()->json([
                'success' => true,
                'message' => 'transaction created',
                'data' => ['transaction_id' => $response['transaction_id']]
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function preApplyTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'card_number' => 'required',
            'expiry' => 'required',
        ]);
        try {
            $postData = $request->only('transaction_id', 'card_number', 'expiry');
            $postData['store_id'] = 7954;
            $response = \Http::withHeaders($this->headers)->post('https://partner.atmos.uz/merchant/pay/pre-apply', $postData)->body();
            return response()->json([
                'success' => true,
                'message' => 'code sent',
                'data' => []
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

    }

    public function applyTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'otp' => 'required',
        ]);
        try {
            $postData = [
                'otp' => $request->otp,
                'transaction_id' => $request->transaction_id,
                'store_id' => 7954,
            ];
            $response = \Http::withHeaders($this->headers)->post('https://partner.atmos.uz/merchant/pay/apply', $postData)->body();
            Invoice::query()->where('transaction_id', $request->transaction_id)->update(['status'=>'payed']);
            return response()->json([
                'success' => true,
                'message' => 'payment completed',
                'data' => []
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

    }
}
