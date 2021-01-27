<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\ShopifyStore;
use App\ShopifyOrder;


class WebhookController extends Controller
{

    public function createorder(Request $request)
    {

        $shop_domain = $request->header('x-shopify-shop-domain');
        $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();

        $new_order = array(
            'user_id' => $shop_details->user_id,
            'store_id' => $shop_details->id,
            'order_id' => $request->id,
            'order_number' => $request->order_number,
            'created_on_shopify' => $request->created_at,
            'test' => $request->test,
            'total_price' => $request->total_price,
            'total_tax' => $request->total_tax,
            'currency' => $request->currency,
            'financial_status' => $request->financial_status,
            'total_discounts' => $request->total_discounts,
            'referring_site' => $request->referring_site,
            'landing_site' => $request->landing_site,
            'cancelled_at' => $request->cancelled_at,
            'total_price_usd' => $request->total_price_usd,
            'discount_applications' => json_encode($request->discount_applications),
            'fulfillment_status' => $request->fulfillment_status,
            'tax_lines' => json_encode($request->tax_lines),
            'refunds' => json_encode($request->refunds),
            'total_tip_received' => $request->total_tip_received,
            'original_total_duties_set' => $request->original_total_duties_set,
            'current_total_duties_set' => $request->current_total_duties_set,
            'shipping_lines' => json_encode($request->shipping_lines),
        );

        ShopifyOrder::create($new_order);
    }

    public function ordersUpdated(Request $request)
    {

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $data = file_get_contents('php://input');
        $verified = $this->verify_webhook($data, $hmac_header);

        if ($verified) {
            $shop_domain = $request->header('x-shopify-shop-domain');
            $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();

            $update_order = array(
                'user_id' => $shop_details->user_id,
                'store_id' => $shop_details->id,
                'order_id' => $request->id,
                'order_number' => $request->order_number,
                'created_on_shopify' => $request->created_at,
                'test' => $request->test,
                'total_price' => $request->total_price,
                'total_tax' => $request->total_tax,
                'currency' => $request->currency,
                'financial_status' => $request->financial_status,
                'total_discounts' => $request->total_discounts,
                'referring_site' => $request->referring_site,
                'landing_site' => $request->landing_site,
                'cancelled_at' => $request->cancelled_at,
                'total_price_usd' => $request->total_price_usd,
                'discount_applications' => json_encode($request->discount_applications),
                'fulfillment_status' => $request->fulfillment_status,
                'tax_lines' => json_encode($request->tax_lines),
                'refunds' => json_encode($request->refunds),
                'total_tip_received' => $request->total_tip_received,
                'original_total_duties_set' => $request->original_total_duties_set,
                'current_total_duties_set' => $request->current_total_duties_set,
                'shipping_lines' => json_encode($request->shipping_lines),
            );
            //  return $update_order;
            ShopifyOrder::updateOrCreate(['order_id' => $request->id], $update_order);
        }
    }

    public function ordersCancelled(Request $request)
    {
    }

    public function ordersDelete(Request $request)
    {
        $order = ShopifyOrder::where('order_id', '=', $request->id);
        $order->is_deleted = true;
        $order->save();
    }
    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, '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', true));
        $result =  hash_equals($hmac_header, $calculated_hmac);
        return $result;
    }
}
