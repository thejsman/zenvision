<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\ShopifyStore;
use App\ShopifyOrder;
use App\ShopifyOrderProduct;
use App\ShopifyProductVariant;
use App\SupplierPayable;
use App\InventoryManagement;
use Cache;

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
            'total_price' => $request->total_price,
            'total_tax' => $request->total_tax,
            'currency' => $request->currency,
            'financial_status' => $request->financial_status,
            'total_discounts' => $request->total_discounts,
            'referring_site' => $request->referring_site,
            'landing_site' => $request->landing_site,
            'cancelled_at' => $request->cancelled_at,
            'total_price_usd' => $request->total_price_usd,
            'discount_applications' => $request->discount_applications,
            'fulfillment_status' => $request->fulfillment_status,
            'tax_lines' => $request->tax_lines,
            'refunds' => $request->refunds,
            'total_tip_received' => $request->total_tip_received,
            'original_total_duties_set' => $request->original_total_duties_set,
            'current_total_duties_set' => $request->current_total_duties_set,
            'shipping_lines' => $request->shipping_lines,
        );

        ShopifyOrder::updateOrCreate([
            'user_id' => $shop_details->user_id, 'store_id' => $shop_details->id, 'order_id' => $request->id, 'order_number' => $request->order_number
        ], $new_order);
        $this->addLineItems($request->line_items, $shop_details->user_id, $shop_details->id, $request->id, $request->order_number);

        //Cache Clear
        Cache::tags(['SHOPIFY:' . $shop_details->user_id])->flush();

        return response('success', 200);
    }

    public function ordersUpdated(Request $request)
    {

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $data = file_get_contents('php://input');
        $verified = $this->verify_webhook($data, $hmac_header);

        if (!$verified) {
            $shop_domain = $request->header('x-shopify-shop-domain');
            $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();

            $order_exists =  ShopifyOrder::where('order_id',  $request->id)->first();

            if ($order_exists) {
                $order_exists->user_id = $shop_details->user_id;
                $order_exists->store_id = $shop_details->id;
                $order_exists->order_id = $request->id;
                $order_exists->order_number = $request->order_number;
                $order_exists->created_on_shopify = $request->created_at;
                $order_exists->total_price = $request->total_price;
                $order_exists->total_tax = $request->total_tax;
                $order_exists->currency = $request->currency;
                $order_exists->financial_status = $request->financial_status;
                $order_exists->total_discounts = $request->total_discounts;
                $order_exists->referring_site = $request->referring_site;
                $order_exists->landing_site = $request->landing_site;
                $order_exists->cancelled_at = $request->cancelled_at;
                $order_exists->total_price_usd = $request->total_price_usd;
                $order_exists->discount_applications = $request->discount_applications;
                $order_exists->fulfillment_status = $request->fulfillment_status;
                $order_exists->tax_lines = $request->tax_lines;
                $order_exists->refunds = $request->refunds;
                $order_exists->total_tip_received = $request->total_tip_received;
                $order_exists->original_total_duties_set = $request->original_total_duties_set;
                $order_exists->current_total_duties_set = $request->current_total_duties_set;
                $order_exists->shipping_lines = $request->shipping_lines;
                $order_exists->save();
                // update line items
                $this->addLineItems($request->line_items, $shop_details->user_id, $shop_details->id, $request->id, $request->order_number, "update");
            }
            //Cache Clear
            Cache::tags(['SHOPIFY:' . $shop_details->user_id])->flush();
        }



        return response('success', 200);
    }

    public function ordersCancelled(Request $request)
    {
        return response('success', 200);
    }

    public function ordersDelete(Request $request)
    {

        $order = ShopifyOrder::where('order_id', $request->id)->first();

        $order->is_deleted = true;
        $order->save();
        return response('success', 200);
    }
    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, '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', true));
        $result =  hash_equals($hmac_header, $calculated_hmac);
        return $result;
    }

    public function addLineItems($line_items, $user_id, $store_id, $order_id, $order_number, $webhook_type = "create")
    {

        foreach ($line_items as $line_item) {
            $cogs = $this->getTotalCost($line_item['variant_id']) * $line_item['fulfillable_quantity'];

            $new_line_item = array(
                'user_id' =>  $user_id,
                'store_id' => $store_id,
                'order_id' => $order_id,
                'order_number' => $order_number,
                'line_item_id' => $line_item['id'],
                'variant_id' => $line_item['variant_id'],
                'title' => $line_item['title'],
                'quantity' => $line_item['fulfillable_quantity'],
                'sku' => $line_item['sku'] ? $line_item['sku']  : 'no_sku',
                'variant_title' => $line_item['variant_title'],
                'fulfillment_service' => $line_item['fulfillment_service'],
                'product_id' => $line_item['product_id'],
                'price' => $line_item['price'],
                'total_cost' => $cogs,
                'total_discount' => $line_item['total_discount'],
                'fulfillment_status' => $line_item['fulfillment_status'],
                'duties' => $line_item['duties'],
                'tax_lines' => $line_item['tax_lines'],
            );

            $this->updateInventory($line_item['variant_id'], $line_item['fulfillable_quantity'], $webhook_type, $order_id, $user_id, $line_item['title'], $order_number, $cogs);

            ShopifyOrderProduct::updateOrCreate(['order_id' => $order_id, 'variant_id' => $line_item['variant_id'], 'product_id' => $line_item['product_id']], $new_line_item);
            //Cache Clear
            Cache::tags(['SHOPIFY:' . $user_id])->flush();
        }
    }

    public function getTotalCost($variant_id)
    {
        $product = ShopifyProductVariant::where('variant_id', $variant_id)->select('cost', 'shipping_cost')->first();
        if ($product == null) {
            return null;
        } else {
            return $product->cost + $product->shipping_cost;
        }
    }
    public function updateInventory($variant_id, $qty, $webhook_type, $order_id = null, $user_id, $product_title, $order_number, $cogs)
    {
        $product = ShopifyProductVariant::where('variant_id', $variant_id)->first();
        if ($product) {
            if ($product->units !== null) {
                if ($webhook_type == "create") {
                    // $product->units = $product->units - $qty;
                    // $product->total_inventory = $product->total_inventory - $product->cost * $qty;
                    // $product->save();
                    $this->addInventory($product, $user_id, $order_number, $qty);
                } else {
                    $order = ShopifyOrderProduct::where('order_id', $order_id)->where('variant_id', $variant_id)->first();
                    if ($order) {
                        // $qty_diff =  $qty - $order->quantity;
                        // $product->units = $product->units - $qty_diff;
                        // $product->total_inventory = $product->total_inventory - $product->cost * $qty_diff;
                        // $product->save();
                        $this->addInventory($product, $user_id, $order_number, $qty);
                    }
                }
            } else {
                $this->addSupplierPayable($user_id, $product_title, $order_number, $cogs);
            }
        }
        //Cache Clear
        Cache::tags(['SHOPIFY:' . $user_id])->flush();
    }

    public function addInventory($product, $user_id, $order_number, $qty)
    {
        if ($product) {
            $inventory_item = array(
                'store_id' => $product->store_id,
                'user_id' => $user_id,
                'shopify_order_number' => $order_number,
                'product_id' => $product->product_id,
                'variant_id' =>  $product->variant_id,
                'inventory_item_id' => $product->inventory_item_id,
                'product_title' => $product->product_title,
                'variant_title' => $product->variant_title,
                'sku' => $product->sku == null ? 'no_sku-2' : $product->sku,
                'color' => $product->color,
                'size' => $product->size,
                'sales_price' => $product->sales_price,
                'cost' => $product->cost,
                'shipping_cost' => $product->shipping_cost,
                'units' => $qty,
                'total_inventory' => -$product->cost * $qty
            );
            InventoryManagement::updateOrCreate(['shopify_order_number' => $order_number, 'product_id' => $product->product_id], $inventory_item);
        }
        //Cache Clear
        Cache::tags(['SHOPIFY:' . $user_id])->flush();
    }
    public function addSupplierPayable($user_id, $product_title, $order_number, $cogs)
    {
        if ($cogs !== null) {
            $data = array(
                'user_id' => $user_id,
                'title' => $product_title,
                'amount' => $cogs,
                'type' => 'shopify',
                'reference_number' => $order_number
            );
            SupplierPayable::create($data);
        }
    }
}
