<?php

namespace App\Http\Controllers\API\v1\Rest\Pay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardController extends Controller
{
    public array $headers;
    public string $partner_base_url;
    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => request()->header('Authorization'),
        ];
        $this->partner_base_url = config('atmos.atmos_partner_base_url');
    }
    public function init(Request $request)
    {
        $request->validate(['card_number' => 'required', 'expiry' => 'required']);
        $postData = [
            'card_number' => $request->card_number,
            'expiry' => $request->expiry,
        ];
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->partner_base_url.'/partner/bind-card/init', $postData);

            if ($response->failed()){
                throw new \Exception('something went wrong');
            }
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate(['transaction_id' => 'required', 'otp' => 'required']);
        $postData = [
            'transaction_id' => $request->transaction_id,
            'otp' => $request->otp,
        ];
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->partner_base_url.'/partner/bind-card/confirm', $postData);
            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function listCards(Request $request)
    {
        $postData = [
            'page' => $request->page,
            'page_size' => $request->page_size,
        ];
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->partner_base_url.'/partner/list-cards', $postData);
            return response()->json([
                'success' => true,
                'data' => $response->json()
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
