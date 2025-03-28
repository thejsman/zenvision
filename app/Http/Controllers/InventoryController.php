<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopifyStore;
use App\ShopifyProductVariant;
use App\InventoryManagement;

use Auth;

class InventoryController extends Controller
{
    public function index()
    {
        return  InventoryManagement::where('user_id', Auth::user()->id)->get();
    }
    //
    public function store(Request $request)
    {
        //fetch the shop domain from the header
        $shop_domain = $request->header('x-shopify-shop-domain');

        //fetch shop detials
        $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();

        //create product array
        $options = $request->options;

        //check if color or size options are set
        foreach ($options as $key => $option) {
            if ($option['name'] == 'Size') {
                $sizeOption = $option['position'];
            }
            if ($option['name'] == 'Color') {
                $colorOption = $option['position'];
            }
        }

        $product = array(
            'store_id' => $shop_details->id,
            'product_id' => $request->id,
            'product_title' => $request->title,

        );
        //for each product variant create record for the table
        foreach ($request->variants as $variant_key => $variant) {

            $product_variant = array(
                'variant_id' => $variant['id'],
                'variant_title' => isset($variant['title']) ? $variant['title'] : "",
                'sku' => isset($variant['sku']) ? $variant['sku'] : 'no_sku',
                'sales_price' => $variant['price'],
                'color' => isset($colorOption) ? $variant['option' . $colorOption] : null,
                "size" => isset($sizeOption) ? $variant['option' . $sizeOption] : null,
                // 'cost' => 0,
                // "shipping_cost" => 0
            );

            $newProduct =  array_merge($product,  $product_variant);

            ShopifyProductVariant::create($newProduct);
        }

        response()->json(['success' => 'success'], 200);
    }

    public function update(Request $request)
    {

        //fetch the shop domain from the header
        $shop_domain = $request->header('x-shopify-shop-domain');

        //fetch shop detials
        $shop_details = ShopifyStore::where('store_url', $shop_domain)->first();

        $productVariant = ShopifyProductVariant::where('inventory_item_id', $request->id)->where('store_id', $shop_details->id)->first();
        if ($productVariant) {
            $productVariant->cost = $request->cost;
            $productVariant->save();
        }

        response()->json(['success' => 'success'], 200);
    }

    public function deleteInventory(Request $request)
    {
        $item = ShopifyProductVariant::find($request->id);
        $item->total_inventory = null;
        $item->units = null;
        $item->save();
    }

    public function editInventory(Request $request)
    {
        $item = InventoryManagement::find($request->id);
        if ($item) {
            $item->cost = $request->cost;
            $item->units = $request->units;
            $item->total_inventory = $request->total_inventory;
            $item->save();
        }
    }
    public function destroy($id)
    {
        InventoryManagement::destroy($id);
    }
}
