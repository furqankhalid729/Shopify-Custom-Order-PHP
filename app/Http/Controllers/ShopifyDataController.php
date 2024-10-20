<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shopify\Clients\Rest;
use Shopify\Utils;
use Shopify\Rest\Admin2024_04\Order;
use Illuminate\Support\Facades\Log;

class ShopifyDataController extends Controller
{
    protected $validZipCodes = [
        '560063', '560030', '560034', '560007', '560092', '560024', 
        '562106', '560045', '560003', '560050', '562107', '560064', 
        '560047', '560026', '560086', '560002', '560070', '560073', 
        '562149', '560053', '560085', '560043', '560017', '560001', 
        '560009', '560025', '560083', '560076', '560004', '560079', 
        '560103', '560046', '562157', '560010', '560049', '560056', 
        '560068', '560093', '560018', '560040', '560097', '560061', 
        '562130', '560067', '560036', '560029', '560062', '560037', 
        '560071', '562125', '560100', '560005', '560065', '560019', 
        '560021', '560085', '560022', '560013', '560087', '560008', 
        '560051', '560102', '560104', '560094', '560066', '560038', 
        '560078', '560006', '560014', '560015', '560041', '560070', 
        '560069', '560011', '560083', '560079', '560079', '560096', 
        '560098', '560095', '560077', '560084', '560054', '560033', 
        '560055', '560059', '560080', '560092', '560012', '560020', 
        '560027', '560042', '560028', '560056', '560052', '560097', 
        '560040', '560091', '560018', '560047', '560064', '560066', 
        '560070', '560078', '560037', '560022'
    ];
    public function orderData(Request $request)
    {
        $store_url = env('STORE_URL');
        $access_token = env('ACCESS_TOKEN');
        $client = new Rest($store_url, $access_token);
        Log::info('Form data submitted:', $request->all());
        $data = $request->input('contact');
        $productId = $data['product_id'] ?? null;
        $name = $data['name'] ?? null;
        $email =  $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $address = $data['address'] ?? null;
        $city = $data['city'] ?? null;
        $zip = $data['zip-code'] ?? null;
        $price = $data['price'] ?? null;
        $state =  "Karnataka";
        if($city == "Mumbai"){
            $state =  "West Bengal";
        }
        if (!in_array($zip, $this->validZipCodes)) {
            return response()->json(['error' => 'Invalid zip code.'], 400);
        }
        $excludeFields = ['name', 'email', 'phone', 'address', 'zip-code','city'];
        $orderNotes = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $excludeFields)) {
                $orderNotes[] = "$key: $value";
            }
        }
        $orderNotesString = implode(", ", $orderNotes);
        
        $orderData = [
            'order' => [
                'line_items' => [
                    [
                        'variant_id' => "$productId",
                        'quantity' => 1,
                        'price' => $price
                    ],
                ],
                'customer' => [
                    'first_name' => "$name",
                    'last_name' => "$name",
                    'email' => "$email",
                    'phone' => "$phone",
                ],
                'billing_address' => [
                    'first_name' => "$name",
                    'last_name' => "$name",
                    'address1' => "$address",
                    'city' => "$city",
                    'province' => "$state",
                    'country' => 'India',
                    'zip' => "$zip",
                ],
                'shipping_address' => [
                    'first_name' => "$name",
                    'last_name' => "$name",
                    'address1' => "$address",
                    'city' => "$city",
                    'province' => "$state",
                    'country' => 'India',
                    'zip' => "$zip",
                ],
                'financial_status' => 'paid',
                'note' => $orderNotesString,
            ],
        ];
        //return response()->json($orderData);
        $response = $client->post('admin/api/2024-04/orders.json', $orderData);
        if ($response->getStatusCode() === 201) {
            $order = json_decode($response->getBody()->getContents(), true);
            return response()->json($order);
        } else {
            echo "Error creating order: " . $response->getBody()->getContents();
        }
    }
}
