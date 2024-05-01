<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
use App\Models\WithDrawInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WithDrawController extends Controller
{
    public string $base_url;
    public function __construct()
    {
        $this->base_url = config('atmos.atmos_base_url');
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => request()->header('Authorization'),
        ];
    }
    public function getToken()
    {
        $consumerKey = config('atmos.withdraw_key');
        $consumerSecret = config('atmos.withdraw_secret');
        $base64Credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $data = [
            'grant_type' => 'client_credentials'
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$base64Credentials,
            'Content-Type' => 'application/json'
        ])->post($this->base_url.'/token', $data);

        if ($response->failed()) {
            return response()->json('something went wrong');
        }
        return response()->json([
            'data' => [
                'token' => $response->json()['access_token'],
                'expires_in' => $response->json()['expires_in']
            ]
        ]);
    }

    public function cardInfo(Request $request)
    {
        $request->validate(['card_number' => 'required']);
        $data = [
            'card_number' => $request->card_number
        ];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/out/1.0.0/asl/info', $data);

        if ($response->failed())
            return $response;

        return response()->json([
            'data' => $response->json()
        ]);
    }

    public function cardDetail(Request $request)
    {
        $request->validate(['card_id' => 'required']);
        $data = [
            'card_id' => $request->card_id
        ];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/out/1.0.0/asl/card/id', $data);

        if ($response->failed())
            return $response;

        return response()->json([
            'data' => $response->json()
        ]);
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required',
            'card_id' => 'required',
        ]);
        $invoice = WithDrawInvoice::query()->create($request->only('user_id', 'amount'));
        $postData = [
            'card_id' => $request->card_id,
            'amount' => $request->amount,
            'ext_id' => $invoice->id
        ];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/out/1.0.0/asl/create', $postData);

        if ($response->failed())
            return $response;

        return response()->json([
            'data' => $response->json()
        ]);
    }

    public function applyTransaction(Request $request)
    {
        $request->validate(['transaction_id' => 'required']);
        $postData = ['transaction_id' => $request->transaction_id];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/out/1.0.0/asl/apply', $postData);
        WithDrawInvoice::where('id', $response->json()['data']['ext_id'])->update(['status' => 'payed']);
        if ($response->failed())
            return $response;

        return response()->json([
            'data' => $response->json()
        ]);
    }

    public function detailsTransaction(Request $request)
    {
        $request->validate(['transaction_id' => 'required']);
        $postData = ['transaction_id' => $request->transaction_id];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/out/1.0.0/asl/id', $postData);

        if ($response->failed())
            return $response;

        return response()->json([
            'data' => $response->json()
        ]);
    }

}
