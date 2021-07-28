<?php

namespace App\Http\Controllers;

use App\Http\CustomRequests;
use App\Jobs\ProcessShopifyGetAllOrders;
use App\Jobs\ProcessShopifyGetAllProducts;
use App\ShopifyOrder;
use App\ShopifyStore;
use Auth;
use Illuminate\Http\Request;

class ShopifyStoreController extends Controller
{

    protected $webhooks = [];

    public function __construct(Request $request)
    {
        $this->webhooks = [
            [
                'topic' => 'orders/create',
                'address' => env('APP_URL', null) . '/api/webhooks/create-order',
                'format' => 'json',
            ],
            [
                'topic' => 'orders/updated',
                'address' => env('APP_URL', null) . '/api/webhooks/orders-updated',
                'format' => 'json',
            ],
            [
                'topic' => 'orders/delete',
                'address' => env('APP_URL', null) . '/api/webhooks/orders-delete',
                'format' => 'json',
            ],
            [
                'topic' => 'products/create',
                'address' => env('APP_URL', null) . '/api/webhooks/product-create',
                'format' => 'json',
            ],
            [
                'topic' => 'products/update',
                'address' => env('APP_URL', null) . '/api/webhooks/product-update',
                'format' => 'json',
            ],
            [
                'topic' => 'app/uninstalled',
                'address' => env('APP_URL', null) . '/api/webhooks/app-uninstalled',
                'format' => 'json',
            ],
            // [
            //     'topic'        => 'inventory_items/create',
            //     'address'    => env('APP_URL', null) . '/api/webhooks/inventory-create',
            //     'format'    => 'json'
            // ],
            [
                'topic' => 'inventory_items/update',
                'address' => env('APP_URL', null) . '/api/webhooks/inventory-update',
                'format' => 'json',
            ],
            [
                'topic' => 'customers/data_request',
                'address' => env('APP_URL', null) . '/api/webhooks/customer-data-request',
                'format' => 'json',
            ],
            [
                'topic' => 'customers/redact',
                'address' => env('APP_URL', null) . '/api/webhooks/customer-redact',
                'format' => 'json',
            ],
            [
                'topic' => 'shop/redact',
                'address' => env('APP_URL', null) . '/api/webhooks/shop-redact',
                'format' => 'json',
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
        $count = ShopifyStore::where('store_url', $store_url)->where('user_id', Auth::user()->id)->where('isDeleted', false)->count();
        return ['count' => $count];
    }

    public function getResponse(Request $request)
    {

        // response code from shopify
        $response_code = $request->input('code');
        // shopify store domain
        $shop_domain = $request->input('shop');
        // generating token
        $state = $request->input('state');

        $access_token = $this->getAccessToken($shop_domain, $response_code);

        if ($access_token != "") {
            $shop_exists = ShopifyStore::where('store_url', $shop_domain)->where('isDeleted', true)->first();

            if ($shop_exists) {
                $shop_exists->api_token = $access_token;
                $shop_exists->isDeleted = false;

                $created_at_min = 0;
                $latest_order = ShopifyOrder::where('store_id', $shop_exists->id)->latest('created_on_shopify')->first();

                if ($latest_order) {
                    $created_at_min = $latest_order;
                }

                $this->registerWebhook($shop_domain, $access_token);
                // $orders = (new GetAllOrders)->getAllOrders($shop_domain, $access_token, $shop_exists->id, $created_at_min);
                // $products = (new GetAllProducts)->getAllProducts($shop_domain, $access_token, $shop_exists->id);
                $shop_exists->save();
                $param = [];
                $param['fields'] = 'id, order_number, name, line_items, created_at,  total_price, total_tax, currency, financial_status, total_discounts, referring_site, landing_site, cancelled_at, total_price_usd, discount_applications, fulfillment_status, tax_lines, refunds, total_tip_received, original_total_duties_set, current_total_duties_set, shipping_address, shipping_lines';
                $param['limit'] = 250;
                $param['since_id'] = 0;
                $param['access_token'] = $access_token;

                // Params for Product variant
                $param_products = [];
                $param_products['fields'] = 'id, title, variants, options';
                $param_products['limit'] = 50;
                $param_products['since_id'] = 0;
                $param_products['access_token'] = $access_token;

                // Dispatch the tasks to Queues
                ProcessShopifyGetAllOrders::dispatch($shop_domain, $param, $shop_exists->id, Auth::user()->id);
                ProcessShopifyGetAllProducts::dispatch($shop_domain, $param_products, $shop_exists->id);

                if (strpos($state, 'mastersheet') !== false) {
                    return redirect()->route('mastersheet', ['shopifyAddAccount' => 'success']);
                } else {
                    return redirect()->route('home', ['shopifyAddAccount' => 'success']);
                }
            } else {
                $store_id = ShopifyStore::updateOrCreate([
                    // 'user_id' => Auth::user()->id,
                    'user_id' => Auth::user()->id,
                    'store_name' => $shop_domain,
                    'store_url' => $shop_domain,
                    'api_token' => $access_token,
                    'isDeleted' => false,
                    'enabled_on_dashboard' => true,
                ]);
                $this->registerWebhook($shop_domain, $access_token);

                // Params for Orders and Ordered products
                $param = [];
                $param['fields'] = 'id, order_number, name, line_items, created_at,  total_price, total_tax, currency, financial_status, total_discounts, referring_site, landing_site, cancelled_at, total_price_usd, discount_applications, fulfillment_status, tax_lines, refunds, total_tip_received, original_total_duties_set, current_total_duties_set, shipping_address, shipping_lines';
                $param['limit'] = 250;
                $param['since_id'] = 0;
                $param['access_token'] = $access_token;

                // Params for Product variant
                $param_products = [];
                $param_products['fields'] = 'id, title, variants, options';
                $param_products['limit'] = 50;
                $param_products['since_id'] = 0;
                $param_products['access_token'] = $access_token;

                // Dispatch the tasks to Queues
                ProcessShopifyGetAllOrders::dispatch($shop_domain, $param, $store_id->id, Auth::user()->id);
                ProcessShopifyGetAllProducts::dispatch($shop_domain, $param_products, $store_id->id);

                if (strpos($state, 'mastersheet') !== false) {
                    return redirect()->route('mastersheet', ['shopifyAddAccount' => 'success']);
                } else {
                    return redirect()->route('home', ['shopifyAddAccount' => 'success']);
                }
            }
        }
    }
    public function getAccessToken($shop_domain = '', $code = '')
    {
        $query = array(
            "client_id" => config('shopify.api_key'),
            "client_secret" => config('shopify.api_secret'),
            "code" => $code,
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
        $access_token = $request->api_token;
        $revoke_url = "https://" . $request->store_url . "/admin/api_permissions/current.json";
        $store->isDeleted = true;
        $store->save();
        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Content-Length: 0",
            "X-Shopify-Access-Token: " . $access_token,
        );

        $handler = curl_init($revoke_url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($handler);
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
    public static function getShopifyStoreBalance($user = null)
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $store_balance = 0;
        foreach ($enabled_on_dashboard as $store_id) {

            $store = ShopifyStore::find($store_id)->getStoreDetails();
            $url = "https://" . $store['store_url'] . "/admin/api/2021-04/shopify_payments/balance.json";
            $access_token = $store['api_token'];
            $response = CustomRequests::getRequest($url, [], $access_token);
            if (!isset($response['errors'])) {
                $store_balance += array_column($response['balance'], 'amount')[0];
            }
        }
        return $store_balance;
    }
    public static function getShopifyStoreReserves($user = null)
    {
        $user = Auth::user();
        $date_max = date("Y-m-d", strtotime('-90 days'));
        $payout_status = array("scheduled", "in_transit", "paid", "failed");
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $store_reserves = 0;
        foreach ($enabled_on_dashboard as $store_id) {

            $store = ShopifyStore::find($store_id)->getStoreDetails();
            $url = "https://" . $store['store_url'] . "/admin/api/2021-01/shopify_payments/payouts.json?date_max=" . $date_max;
            $access_token = $store['api_token'];
            $response = CustomRequests::getRequest($url, [], $access_token);
            if (!isset($response['errors'])) {
                if (isset($response['payouts'])) {
                    foreach ($response['payouts'] as $payout) {
                        if (in_array($payout->status, $payout_status)) {
                            $store_reserves += $payout['summary']->reserved_funds_gross_amount;
                        }
                    }
                }
            }
        }
        return $store_reserves;
    }

    public function getDisputes()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        //check if there are enabled/active stores
        if (!$enabled_on_dashboard->isEmpty()) {
            foreach ($enabled_on_dashboard as $store_id) {
                $store = ShopifyStore::find($store_id);
                $url = "https://" . $store->store_url . "/admin/api/2021-01/shopify_payments/disputes.json?status=lost,charge_refunded";
                $access_token = $store->api_token;
                $response = CustomRequests::getRequest($url, [], $access_token);
                return $response;
            }
        } else {
            return ["disputes" => []];
        }
    }

    public function getRefundTotal(Request $request)
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        $refund_total = 0;
        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);

            $refund_total += $store->getRefundTotal($request->start_date, $request->end_date);
        }
        return $refund_total;
    }
}
