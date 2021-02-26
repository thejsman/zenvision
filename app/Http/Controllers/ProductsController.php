<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopifyStore;
use App\ShopifyProductVariant;

class ProductsController extends Controller
{
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
                'sku' => isset($variant['sku']) ? $variant['sku'] : null,
                'sales_price' => $variant['price'],
                'color' => isset($colorOption) ? $variant['option' . $colorOption] : null,
                "size" => isset($sizeOption) ? $variant['option' . $sizeOption] : null,
                'inventory_item_id' => $variant['inventory_item_id']
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

        //find if size and color options are set
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

        //create product array
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
                'sku' => isset($variant['sku']) ? $variant['sku'] : null,
                'sales_price' => $variant['price'],
                'color' => isset($colorOption) ? $variant['option' . $colorOption] : null,
                "size" => isset($sizeOption) ? $variant['option' . $sizeOption] : null,
                'inventory_item_id' => $variant['inventory_item_id']
                // 'cost' => 0,
                // "shipping_cost" => 0
            );
            $productUpdate =  array_merge($product, $product_variant);

            //check if the varian already exists in the db then update or else create a new record
            ShopifyProductVariant::updateOrCreate(['store_id' => $shop_details->id, 'product_id' =>  $request->id, 'variant_id' => $variant['id']], $productUpdate);
        }
        response()->json(['success' => 'success'], 200);
    }
}
