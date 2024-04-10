<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;

class GetTokenController extends Controller
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

    public function getToken()
    {
        $response = \Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic Base64(consumer-tmh7AEwzgzgjNFVUDsuw9SwshIEa:consumer-a2CfeTskblqVhBZ_baUKBqMDRjMa)',
            'Host' => 'partner.atmos.uz',
            'Content-Length' => 29,
        ])
            ->post('https://partner.atmos.uz/token', [
                'grant_type' => 'client_credentials'
            ])->body();
        return response()->json([
            'success' => true,
            'data' => ['token' => $response['access_token']]
        ]);

    }

}
