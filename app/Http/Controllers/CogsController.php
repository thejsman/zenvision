<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopifyProductVariant;
use App\ShopifyOrderProduct;
use App\User;
use Auth;

class CogsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();

        $products = ShopifyProductVariant::select('id', 'variant_id', 'product_title', 'sku', 'color', 'size', 'sales_price', 'cost', 'shipping_cost')->whereIn('store_id', $enabled_on_dashboard)->get();
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
    }

    private function updateProductCogs($varian_id, $cogs)
    {
        $lineItems = ShopifyOrderProduct::where('variant_id', $varian_id)->get();
        foreach ($lineItems as $lineItem) {
            $lineItem_cogs = $lineItem->quantity * $cogs;
            $lineItem->cogs = $lineItem_cogs;
            $lineItem->save();
        }
    }
    public function showCogsIcon()
    {
        $user = Auth::user();
        $enabled_on_dashboard = $user->getEnabledShopifyStores();
        return  ShopifyOrderProduct::whereIn('store_id', $enabled_on_dashboard)->whereNull('cogs')->count();
    }
}
