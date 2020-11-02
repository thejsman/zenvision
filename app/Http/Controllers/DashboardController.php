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
    public function index() {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        $numberOfProducts = 0;
        $cogs = 0;
        $refundTotal = 0;
        $fb_spend = [];
        $fb_ad_accounts = $user->getAdAccounts();

        foreach($fb_ad_accounts as $key => $fb_ad_account) {
            $url_account_data ="https://graph.facebook.com/v8.0/" . $fb_ad_account->ad_account_id ."/?fields=insights&access_token=". $fb_ad_account->access_token;
            $url2 ="https://graph.facebook.com/v8.0/act_2800689140251659/insights?&time_interval={%22since%22:%222020-07-15%22,%22until%22:%222020-10-14%22}&time_increment=1&access_token=EAAoUXIzcZBNUBAMVOq3OSrGYbNTrkeemc8ZCLsK3geCh3uZC4jqIbnYnzJAuYJs4uv4AhkCgdpu1GvpYbK0V75LTZA3ZBsZAECbALvdqqQ3FER75QhjiuRZBIF8Svs7EyxEf9BcFBsOhZCGW160MDGZAAhXCYJ2DuYgoQ4gbzmZAONjgZDZD";

            $spend =  CustomRequests::getRequest($url2,"","");
             $fb_spend =$spend['data'];

        }
        //check Currency
        // https://openexchangerates.org/api/convert/19999.95/GBP/EUR?app_id=YOUR_APP_ID

        foreach($enabled_on_dashboard as $store_id) {
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
            'fb_ad_accounts' => $fb_ad_accounts
        ];
    }

    // Funciton to get abandoned cart count
    public function getAbandonedCartCount(Request $request) {

            if($request->filled('store_ids')) {
                $store_ids =  $request->store_ids;

                $abandoned_cart_count = 0;
                if(count($store_ids) > 0){
                    foreach($store_ids as $store_id) {
                        $store = ShopifyStore::find($store_id)->getStoreDetails();
                        $url = "https://". $store['store_url']."/admin/api/2020-07/checkouts/count.json";
                        $access_token = $store['api_token'];
                        $response = CustomRequests::getRequest($url,[], $access_token );

                        $abandoned_cart_count +=  $response['count'];
                    }
                }
                return $abandoned_cart_count;
            } else {
                return 0;
            }

    }
}
