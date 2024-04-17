<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
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
            'Authorization' => $base64Credentials,
            'Content-Type' => 'application/json'
        ])->post($this->base_url.'/token', $data);

        if ($response->failed()) {
            return response()->json('something went wrong');
        }
        return response()->json([
            'data' => ['token' => $response->json()['access_token']]
        ]);
    }

    public function cardInfo(Request $request)
    {
        $request->validate(['card_number' => 'required']);
        $data = [
            'card_number' => $request->card_number
        ];
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/info', $data);

        if ($response->failed()) {
            return response()->json('something went wrong');
        }
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
        $response = Http::withHeaders($this->headers)->post($this->base_url.'/card/id', $data);

        if ($response->failed()) {
            return response()->json('something went wrong');
        }
        return response()->json([
            'data' => $response->json()
        ]);
    }

}
