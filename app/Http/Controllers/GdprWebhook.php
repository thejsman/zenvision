<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopifyStore;
use App\ShopifyOrder;

class GdprWebhook extends Controller
{
    public function CustomerDataRequest(Request $request)
    {
        $shop_domain = $request->shop_domain;
        $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();
        if ($shop_details) {
            $orders = [];
            foreach ($request->orders_requested as $req_order) {
                $order =  ShopifyOrder::where('order_number', $req_order)->where('store_id', $shop_details->id)->get()->toArray();
                $orders = array_merge($orders, $order);
            }
            return response()->json(['status' => 'success', 'data_request' => $request->data_request, 'data' => $orders], 200);
        } else {
            return response()->json(['status' => 'success', 'data_request' => $request->data_request, 'message' => 'Data not found', 'data' => []], 200);
        }
    }

    public function CustomerRedactRequest(Request $request)
    {
        $shop_domain = $request->shop_domain;
        $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();
        if ($shop_details) {
            $orders_redacted = [];
            foreach ($request->orders_to_redact as $req_order) {
                $response = ShopifyOrder::where('order_number', $req_order)->where('store_id', $shop_details->id)->delete();

                if ($response) {
                    array_push($orders_redacted, $req_order);
                }
            }
            return response()->json(['status' => 'success', 'orders_redacted' => $orders_redacted], 200);
        } else {
            return response()->json(['status' => 'success',  'message' => 'Data not found',], 200);
        }
    }
}
