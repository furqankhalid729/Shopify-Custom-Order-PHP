<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shopify\Clients\Rest;
use Illuminate\Support\Facades\Log;

class ShopifyDataController extends Controller
{
    public function orderData(Request $request){
        $store_url = env('STORE_URL');
        $access_token = env('ACCESS_TOKEN');
        $client = new Rest($store_url, $access_token);
        //dd($client);
        $response = $client->get(path: 'orders');
        $body = $response->getDecodedBody();
        dd($body);
        return response()->json($response->getBody() );
    }
}
