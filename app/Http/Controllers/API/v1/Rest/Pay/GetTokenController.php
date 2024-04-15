<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class GetTokenController extends Controller
{
    public function getToken()
    {
        $consumerKey = config('atmos.consumer_key');
        $consumerSecret = config('atmos.consumer_secret');
        $base64Credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $data = [
            'grant_type' => 'client_credentials'
        ];

        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . $base64Credentials,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post('https://partner.atmos.uz/token', $data);

        if ($response->successful()) {
            return response()->json([
                'data' => ['token' => $response->json()['access_token']]
            ]);
        }
    }
}
