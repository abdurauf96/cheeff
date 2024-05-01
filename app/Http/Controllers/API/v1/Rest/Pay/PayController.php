<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pay\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayController extends Controller
{
    public array $headers;
    public string $base_url;
    public function __construct()
    {
        $this->base_url = config('atmos.atmos_partner_base_url');
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => request()->header('Authorization'),
            'Host' => 'partner.atmos.uz',
        ];
    }

    public function createTransaction(InvoiceRequest $request)
    {
        $postData = [
            'account' => $request->user_id,
            'amount' => $request->amount,
            'store_id' => config('atmos.store_id'),
        ];

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->base_url.'/merchant/pay/create', $postData);
            if ($response->successful()){
                $invoiceData = $request->validated();
                $invoiceData['transaction_id'] = $response->json()['transaction_id'];
                Invoice::create($invoiceData);
                return response()->json([
                    'success' => true,
                    'message' => 'transaction created',
                    'data' => ['transaction_id' => $response->json()['transaction_id']]
                ]);
            }

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
            'card_token' => 'required',
        ]);
        try {
            $postData = $request->only('transaction_id', 'card_token');
            $postData['store_id'] = config('atmos.store_id');
            $response = Http::withHeaders($this->headers)->post($this->base_url.'/merchant/pay/pre-apply', $postData);
            if ($response->failed())
                throw new \Exception('something went wrong');
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
                'store_id' => config('atmos.store_id'),
            ];
            Http::withHeaders($this->headers)->post($this->base_url.'/merchant/pay/apply', $postData);
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
