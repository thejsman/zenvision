<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ShopifyStore;
use App\ShopifyOrder;
use App\Http\CustomRequests;

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
            'refund_total' => $refundTotal
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
