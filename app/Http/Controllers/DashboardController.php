<?php

namespace App\Http\Controllers;

use App\Http\CustomRequests;
use App\ShopifyOrder;
use App\ShopifyOrderProduct;
use App\ShopifyStore;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //Function to get store sata
    public function index(Request $request)
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        $numberOfProducts = 0;
        $cogs = 0;
        $refundTotal = 0;
        $fb_spend = [];

        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $numberOfProducts += $store->getOrderedProductCount();
            $orders = array_merge($orders, $store->getOrders($request->start_date, $request->end_date)->toArray());
            $refundTotal += $store->getRefundTotal();
        }

        return [
            'enabled_on_dashboard' => $enabled_on_dashboard,
            'number_of_products' => $numberOfProducts,
            'orders' => $orders,
            'refund_total' => $refundTotal,
            'fb_spend' => $fb_spend,
            'fb_ad_accounts' => [],
        ];
    }

    public function getShopifyStoreOrders(Request $request)
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $orders = array_merge($orders, $store->getOrders($request->start_date, $request->end_date)->toArray());
        }
        return $orders;
    }

    // Funciton to get abandoned cart count
    public function getAbandonedCartCount(Request $request)
    {

        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $abandoned_cart_count = 0;
        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $url = "https://" . $store->store_url . "/admin/api/2020-07/checkouts/count.json";
            $access_token = $store->api_token;
            $response = CustomRequests::getRequest($url, [], $access_token);
            $abandoned_cart_count += $response['count'];
        }
        return $abandoned_cart_count;
    }

    public static function mastersheet($user = null)
    {

        if (empty($user)) {
            $user = Auth::user();
        };

        //return values
        $total_cash = 0;
        $total_inventory = 0;
        $total_reserves = 0;
        $total_credit_card = 0;
        $total_supplier_payable = 0;

        //Funciton to get Shopify store balance
        $shopify_balance = self::getShopifyStoreBalance($user);
        // will call a funciton to get available bank cash
        $cash_bank_account = 0;
        // will call a funciton to get available paypal balance
        $cash_paypal = 0;
        // will call a funciton to get available stripe balance
        $cash_stripe = 0;

        $total_cash += $shopify_balance + $cash_bank_account + $cash_paypal + $cash_stripe;

        return [
            'total_cash' => $total_cash,
            'total_inventory' => $total_inventory,
            'total_reserves' => $total_reserves,
            'total_credit_card' => $total_credit_card,
            'total_supplier_payable' => $total_supplier_payable,
        ];
    }
    public function msdebts($user = null)
    {
        //function call to get Credit Card Total
        $debts_credit_card = 0;
        //function call to get Supplier Payable
        $debts_supplier_payable = 0;

        return [
            'debts_credit_card' => $debts_credit_card,
            'debts_supplier_payable' => $debts_supplier_payable,
        ];
    }

    public static function getShopifyStoreBalance($user = null)
    {

        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $store_balance = 0;
        foreach ($enabled_on_dashboard as $store_id) {

            // Commented for Development: to save network requests

            // $store = ShopifyStore::find($store_id)->getStoreDetails();
            // $url = "https://" . $store['store_url'] . "/admin/api/2021-01/shopify_payments/balance.json";
            // $access_token = $store['api_token'];
            // $response = CustomRequests::getRequest($url, [], $access_token);
            // $store_balance +=  array_column($response['balance'], 'amount')[0];
        }
        return $store_balance;
    }

    public function getAvgUnitsPerOrder()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        $orders = ShopifyOrder::whereIn('store_id', $enabled_on_dashboard)->where('is_deleted', false)->whereIn('financial_status', ['paid', 'pending', 'partially_paid'])->pluck('order_id');
        if (count($orders)) {
            $productsCount = ShopifyOrderProduct::whereIn('order_id', $orders)->sum('quantity');
            return round($productsCount / count($orders), 2);
        } else {
            return 0;
        }
    }
}
