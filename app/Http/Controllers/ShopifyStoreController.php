<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ShopifyStore;
use App\Http\CustomRequests;
use App\Http\GetAllOrders;
use App\Http\GetAllProducts;

class ShopifyStoreController extends Controller

{

    protected $webhooks = [];

    public function __construct(Request $request)
    {
        $this->webhooks = [
            [
                'topic'        => 'orders/create',
                'address'    => env('APP_URL', null) . '/api/webhooks/create-order',
                'format'    => 'json'
            ],
            [
                'topic'        => 'orders/updated',
                'address'    => env('APP_URL', null) . '/api/webhooks/orders-updated',
                'format'    => 'json'
            ],
            [
                'topic'        => 'orders/delete',
                'address'    => env('APP_URL', null) . '/api/webhooks/orders-delete',
                'format'    => 'json'
            ],
            [
                'topic'        => 'products/create',
                'address'    => env('APP_URL', null) . '/api/webhooks/product-create',
                'format'    => 'json'
            ],
            [
                'topic'        => 'products/update',
                'address'    => env('APP_URL', null) . '/api/webhooks/product-update',
                'format'    => 'json'
            ],

        ];
    }


    public function getStores()
    {
        return Auth::user()->stores;
    }

    // validate Store url method
    public function validateUrl(Request $request)
    {
        $store_url = $request->store_url;
        $count = ShopifyStore::where('store_url', $store_url)->where('user_id', Auth::user()->id)->count();
        return ['count' => $count];
    }

    public function getResponse(Request $request)
    {
        // response code from shopify
        $response_code = $request->input('code');
        // shopify store domain
        $shop_domain = $request->input('shop');
        // generating token
        $access_token = $this->getAccessToken($shop_domain, $response_code);

        if ($access_token != "") {
            $store_id = ShopifyStore::firstOrCreate([
                'user_id'    => Auth::user()->id,
                'store_name' => $shop_domain,
                'store_url'    => $shop_domain,
                'api_token'    => $access_token
            ]);

            $this->registerWebhook($shop_domain, $access_token);
            $orders = (new GetAllOrders)->getAllOrders($shop_domain, $access_token, $store_id->id);
            $products = (new GetAllProducts)->getAllProducts($shop_domain, $access_token, $store_id->id);
        }


        return redirect('/');
    }
    public function getAccessToken($shop_domain = '', $code = '')
    {

        $query = array(
            "client_id" => config('shopify.api_key'), // Your API key
            "client_secret" => config('shopify.api_secret'), // Your app credentials (secret key)
            "code" => $code // Grab the access key from the URL
        );

        $access_token_url = "https://" . $shop_domain . "/admin/oauth/access_token";

        //send curl request
        $result = CustomRequests::postRequest($access_token_url, $query);

        // Store the access token
        $access_token = $result['access_token'];

        return $access_token;
    }
    public function toggleStore(Request $request)
    {
        $store = ShopifyStore::find($request->id);
        $store->enabled_on_dashboard = !$store->enabled_on_dashboard;
        $store->save();
    }
    public function destroy(Request $request)
    {
        $store = ShopifyStore::find($request->id);
        $store->isDeleted = true;
        $store->save();

        $access_token = $request->api_token;
        $revoke_url   = "https://" . $request->store_url . "/admin/api_permissions/current.json";

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Content-Length: 0",
            "X-Shopify-Access-Token: " . $access_token
        );

        $handler = curl_init($revoke_url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);

        $result =  curl_exec($handler);
        if (!curl_errno($handler)) {
            $info = curl_getinfo($handler);
        }
        curl_close($handler);
    }
    private function registerWebhook($shop_domain = '', $access_token = '')
    {
        // shopify API url for webhook

        $curl_url = "https://" . $shop_domain . "/admin/webhooks.json";

        // iterate webhook array
        foreach ($this->webhooks as $key => $webhook) {

            $curl_data = array('webhook' => $webhook);
            $response = CustomRequests::postRequest($curl_url, $curl_data, $access_token);
        }
    }
}
