<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ShopifyStore;
use App\ShopifyOrder;
use App\Http\CustomRequests;
use App\FacebookAd;

class DashboardController extends Controller
{
    //Function to get store sata
    public function index()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        $numberOfProducts = 0;
        $cogs = 0;
        $refundTotal = 0;
        $fb_spend = [];
        $fb_ad_accounts = $user->getAdAccounts();
        $paypalAccounts = $user->getPaypalAccounts();
        $paypalTransactions = $this->getPaypalTransactions();

        foreach ($fb_ad_accounts as $key => $fb_ad_account) {
            $url_account_data = "https://graph.facebook.com/v8.0/" . $fb_ad_account->ad_account_id . "/?fields=insights&access_token=" . $fb_ad_account->access_token;
            $url2 = "https://graph.facebook.com/v8.0/act_2800689140251659/insights?&time_interval={%22since%22:%222020-07-15%22,%22until%22:%222020-10-14%22}&time_increment=1&access_token=EAAoUXIzcZBNUBAMVOq3OSrGYbNTrkeemc8ZCLsK3geCh3uZC4jqIbnYnzJAuYJs4uv4AhkCgdpu1GvpYbK0V75LTZA3ZBsZAECbALvdqqQ3FER75QhjiuRZBIF8Svs7EyxEf9BcFBsOhZCGW160MDGZAAhXCYJ2DuYgoQ4gbzmZAONjgZDZD";

            $spend =  CustomRequests::getRequest($url2, "", "");
            $fb_spend = $spend['data'];
        }
        //check Currency
        // https://openexchangerates.org/api/convert/19999.95/GBP/EUR?app_id=YOUR_APP_ID

        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $numberOfProducts += $store->getOrderedProductCount();
            $orders = array_merge($orders, $store->getOrders()->toArray());
            $refundTotal += $store->getRefundTotal();
        }

        return [
            'enabled_on_dashboard' => $enabled_on_dashboard,
            'number_of_products' => $numberOfProducts,
            'orders' => $orders,
            'refund_total' => $refundTotal,
            'fb_spend' => $fb_spend,
            'fb_ad_accounts' => $fb_ad_accounts,
            'paypalAccounts' => $paypalAccounts,
            'paypalTransactions' => $paypalTransactions
        ];
    }

    public function getEnabledShopifyStores()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        $numberOfProducts = 0;
        $refundTotal = 0;

        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $numberOfProducts += $store->getOrderedProductCount();
            $refundTotal += $store->getRefundTotal();
        }
    }
    public function getPaypalTransactions()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/reporting/transactions?start_date=2020-12-01T00:00:00-0700&end_date=2020-12-30T23:59:59-0700&fields=transaction_info&transaction_type=T0000');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer A23AAIiTKgWqZiFhSx3kJPN7DsmbM5vnhbkhSAprq-DoLXYnC0s_aUyJ5HTvXnJgmvnJXggDX5pUK2P7-CvRp3Mn5AmtfqVbA';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
        $response = json_decode($result, true);
        if (array_key_exists('transaction_details', $response)) {
            return $response['transaction_details'];
        } else {
            return [];
        }
    }

    // Funciton to get abandoned cart count
    public function getAbandonedCartCount(Request $request)
    {
        $abandoned_cart_count = 0;
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        if (count($enabled_on_dashboard) > 0) {
            foreach ($enabled_on_dashboard as $store_id) {
                $store = ShopifyStore::find($store_id)->getStoreDetails();
                $url = "https://" . $store['store_url'] . "/admin/api/2020-07/checkouts/count.json";
                $access_token = $store['api_token'];
                $response = CustomRequests::getRequest($url, [], $access_token);

                $abandoned_cart_count +=  $response['count'];
            }
        } else {
            return 0;
        }
        return $abandoned_cart_count;
    }
    public function getShopifyData()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        $numberOfProducts = 0;
        $refundTotal = 0;

        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $numberOfProducts += $store->getOrderedProductCount();
            $orders = array_merge($orders, $store->getOrders()->toArray());
            $refundTotal += $store->getRefundTotal();
        }

        return [
            'enabled_on_dashboard' => $enabled_on_dashboard,
            'number_of_products' => $numberOfProducts,
            'orders' => $orders,
            'refund_total' => $refundTotal,
        ];
    }
}
