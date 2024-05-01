<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class GetTokenController extends Controller
{
    public function token()
    {
        $consumerKey = config('atmos.consumer_key');
        $consumerSecret = config('atmos.consumer_secret');
        $base64Credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $response = $this->getToken($base64Credentials);
        if ($response->successful()) {
            return response()->json([
                'data' => ['token' => $response->json()['access_token']]
            ]);
        }
    }

    public function tokenCard()
    {
        $consumerKey = config('atmos.consumer_key_for_card');
        $consumerSecret = config('atmos.consumer_secret_for_card');
        $base64Credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $response = $this->getToken($base64Credentials);
        if ($response->successful()) {
            return response()->json([
                'data' => [
                    'token-card' => $response->json()['access_token'],
                    'expires_in' => $response->json()['expires_in']
                ]
            ]);
        }
    }

    public function getToken($base64Credentials)
    {
        $data = [
            'grant_type' => 'client_credentials'
        ];

        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . $base64Credentials,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post(config('atmos.atmos_partner_base_url').'/token', $data);
        return $response ;
    }
}
