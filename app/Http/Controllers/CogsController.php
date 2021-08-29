<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopifyProductVariant;
use App\ShopifyOrderProduct;
use Cache;
use Auth;

class CogsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        $products = ShopifyProductVariant::select('id', 'variant_id', 'product_title',  'sku', 'color', 'size', 'sales_price', 'cost', 'shipping_cost', 'units', 'total_inventory')->whereIn('store_id', $enabled_on_dashboard)->get();
        return [
            'products' => $products
        ];
    }
    public function indexMS()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStoresMS();

        $products = ShopifyProductVariant::select('id', 'variant_id', 'product_title',  'sku', 'color', 'size', 'sales_price', 'cost', 'shipping_cost', 'units', 'total_inventory')->whereIn('store_id', $enabled_on_dashboard)->get();
        return [
            'products' => $products
        ];
    }
    public function update(Request $request)
    {

        $products = $request->toArray();

        foreach ($products as $key => $value) {
            ShopifyProductVariant::find($value['id'])->update($value);
            $cogs = $value['cost'] + $value['shipping_cost'];
            $this->updateProductCogs($value['variant_id'], $cogs);
        }
        //Cache Clear
        Cache::tags(['SHOPIFY:' . Auth::user()->id])->flush();
    }

    private function updateProductCogs($varian_id, $cogs)
    {
        $lineItems = ShopifyOrderProduct::where('variant_id', $varian_id)->get();
        foreach ($lineItems as $lineItem) {
            $lineItem_cogs = $lineItem->quantity * $cogs;
            $lineItem->total_cost = $lineItem_cogs;
            $lineItem->save();
        }
    }
    public function showCogsIcon(Request $request)
    {
        return Cache::tags(['SHOPIFY:' . Auth::user()->id])->remember('COGS_ICON_' . $request->start_date . '_' . $request->end_date, env('REDIS_TTL'), function () {
            $user = Auth::user();
            $enabled_on_dashboard = $user->getEnabledShopifyStores();
            $cost_count =  ShopifyProductVariant::whereIn('store_id', $enabled_on_dashboard)->whereNull('cost')->count();
            $shipping_count = ShopifyProductVariant::whereIn('store_id', $enabled_on_dashboard)->whereNull('shipping_cost')->count();

            return $cost_count + $shipping_count;
        });
    }
}
