<?php

namespace App\Http\Controllers;

use App\Http\CustomRequests;
use App\ShopifyOrder;
use App\ShopifyOrderProduct;
use App\ShopifyStore;
use Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CreditCardController;

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
            $orders = array_merge($orders, $store->getOrders($request->s_date, $request->e_date)->toArray());
        }

        return $orders;
    }
    public function getShopifyStoreAllOrders(Request $request)
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        $orders = [];
        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $orders = array_merge($orders, $store->getAllOrders()->toArray());
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
        $total_credit_card = CreditCardController::getCreditCardliabilities($user);
        $total_supplier_payable = self::getTotalCogs($user);

        //Funciton to get Shopify store balance
        $shopify_balance = self::getShopifyStoreBalance($user);

        // will call a funciton to get available bank cash
        $cash_bank_account = BankAccountController::getAccountBalance($user);


        // will call a funciton to get available paypal balance
        $cash_paypal = 0;
        // will call a funciton to get available stripe balance
        $cash_stripe = self::getStripeBalance($user);;

        $total_cash = $shopify_balance + $cash_bank_account + $cash_paypal + $cash_stripe;

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

    public function getAvgUnitsPerOrder(Request $request)
    {
        if (count($request->order_ids)) {
            $productsCount = ShopifyOrderProduct::whereIn('order_id', $request->order_ids)->sum('quantity');
            return round($productsCount / count($request->order_ids), 2);
        } else {
            return 0;
        }
    }


    public static function getStripeBalance($user)
    {
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $availableBalance = 0;
        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/balance');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                //Windows Dev system disable SSL
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $headers = array(
                    'Stripe-Account:' . $account->stripe_user_id,
                    'Authorization: Bearer ' . $account->access_token,
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    return [];
                }
                curl_close($ch);
                $response = json_decode($result, true);
                $availableBalance += $response['available'][0]['amount'] / 100;
            }
            return $availableBalance;
        } else {
            return $availableBalance;
        }
    }

    public static function getTotalCogs($user)
    {
        $cogsTotal = 0;

        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        foreach ($enabled_on_dashboard as $store_id) {
            $store = ShopifyStore::find($store_id);
            $cogsTotal += $store->getAllOrdersCogs();
        }
        return $cogsTotal;
    }
}
