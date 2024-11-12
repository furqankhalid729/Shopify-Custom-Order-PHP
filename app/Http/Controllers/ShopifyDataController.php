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
        '560069', '560011', '560083', '560079', '560096', '560098', 
        '560095', '560077', '560084', '560054', '560033', '560055', 
        '560059', '560080', '560092', '560012', '560020', '560027', 
        '560042', '560028', '560056', '560052', '560097', '560040', 
        '560091', '560018', '560047', '560064', '560066', '560070', 
        '560078', '560037', '560022', '700020', '700046', '700027', 
        '700014', '700044', '700086', '700022', '700019', '700007', 
        '700008', '700018', '700034', '700010', '700054', '700025', 
        '700066', '700032', '700042', '700012', '700088', '700073', 
        '700017', '700054', '700073', '700001', '700141', '700031', 
        '700013', '700029', '700107', '700016', '700069', '700021', 
        '700047', '700024', '700075', '700095', '700078', '700082', 
        '700072', '700069', '700033', '700078', '700061', '700034', 
        '700068', '700085', '700020', '700082', '700031', '700053', 
        '700063', '700026', '700099', '700089', '700061', '700026', 
        '700001', '700023', '700073', '700001', '700020', '700045', 
        '700029', '700025', '700014', '700071', '700060', '700141', 
        '700071', '700027', '700013', '700099', '700053', '700047', 
        '700011', '700027', '700040', '700053', '700087', '700001', 
        '700024', '700034', '700094', '700017', '700016', '700060', 
        '700009', '700063', '700041', '700032', '700054', '700072', 
        '700026', '700001', '700018', '700001', '700047', '700009', 
        '700044', '700054', '700025', '700029', '700092', '700040', 
        '700001', '700040', '700071', '700094', '700026', '700038', 
        '700015', '700012', '700075', '700075', '700029', '700061', 
        '700015', '700014', '700034', '700017', '700041', '700043', 
        '700072', '700043', '700026', '700007', '700010', '700033', 
        '700024', '700014', '700015', '700088', '700088', '700001', 
        '700063', '700073', '700033', '700001', '700075', '700001', 
        '700062', '700023', '700001', '700012','700120','700121',
        '700122', '700123', '700124', '700125','700126'
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
            return redirect('https://cashito.in/pages/thank-you?status=zip-error');
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
                'phone' => "$phone"
            ],
        ];
        Log::info('ORder data Before:', $orderData);
        //return response()->json($orderData);
        $response = $client->post('admin/api/2024-04/orders.json', $orderData);
        Log::info('ORder data submit:', [$response->getBody()]);
        if ($response->getStatusCode() === 201) {
            $order = json_decode($response->getBody()->getContents(), true);
            return redirect('https://cashito.in/pages/thank-you?status=success');
        } else {
            return redirect('https://cashito.in/pages/thank-you?status=error');
        }
    }
}
