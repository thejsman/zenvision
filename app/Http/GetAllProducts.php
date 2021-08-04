<?php

namespace App\Http;

use Illuminate\Http\Request;
use App\ShopifyOrder;
use App\ShopifyProductVariant;
use Auth;

class GetAllProducts

{
    public function getAllProducts($shop_domain, $access_token, $store_id)
    {

        $param = [];

        $param['fields'] = 'id, title, variants, options';


        // products record per page
        $param['limit'] = 50;

        $param['since_id'] = 0;

        $param['access_token'] = $access_token;

        return $this->getProductsHistory($shop_domain, $param, $store_id);
    }

    private function getProductsHistory($shop_domain = '', $param = [], $store_id = '')
    {
        // initialize variable
        $nextPageToken = null;

        do {
            // check if next page token is not null
            if ($nextPageToken != null) {
                // remove since id if page info parameter set
                unset($param['since_id']);
            }
            // next page token
            $param['page_info'] = $nextPageToken;

            // shopify product count api
            $products_url = 'https://' . $shop_domain . '/admin/api/2020-07/products.json' . '?' . http_build_query($param);

            // get products data
            $products = $this->shopRequest('get', $products_url);

            // check if products response exist in request
            if (isset($products['products'])) {
                // check if no data found
                if (!empty($products['products'])) {
                    // iterate each product
                    foreach ($products['products'] as $product_key => $product) {

                        $options = $product['options'];

                        foreach ($options as $key => $option) {
                            if ($option['name'] == 'Size') {
                                $sizeOption = $option['position'];
                            }
                            if ($option['name'] == 'Color') {
                                $colorOption = $option['position'];
                            }
                        }

                        //product processing here
                        $new_product = array(
                            'store_id' => $store_id,
                            'product_id' => $product['id'],
                            'product_title' => $product['title'],
                        );
                        foreach ($product['variants'] as $variant_key => $variant) {

                            $inventory_url = 'https://' . $shop_domain . '/admin/api/2021-01/inventory_items.json?ids=' . $variant['inventory_item_id'] . '?' . http_build_query($param);
                            $inventory = $this->shopRequest('get', $inventory_url);
                            $cost = $inventory['products'][0]['cost'];
                            $product_variant = array(
                                'variant_id' => $variant['id'],
                                'variant_title' => $variant['title'],
                                'sku' => isset($variant['sku']) ? $variant['sku'] : 'no_sku',
                                'sales_price' => $variant['price'],
                                'inventory_item_id' => $variant['inventory_item_id'],
                                'color' => isset($colorOption) ? $variant['option' . $colorOption] : null,
                                "size" => isset($sizeOption) ? $variant['option' . $sizeOption] : null,
                                'cost' => $cost,
                                // "shipping_cost" => 0
                            );
                            $newProduct =  array_merge($new_product,  $product_variant);

                            ShopifyProductVariant::create($newProduct);
                        }
                    }
                } else {
                    break;
                }
            }

            // next page token
            $nextPageToken = $products['next']['page_token'] ?? null;
        } while ($nextPageToken != null);
        return $products;
    }

    private function shopRequest($method = '', $url = '')
    {

        try {
            // http client object
            $client = new \GuzzleHttp\Client();
            // api request
            $api_url = $url;

            $parameters = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ];

            // http request
            $response = $client->request($method, $api_url, $parameters);
            // get response headers
            $responseHeaders = $response->getHeaders();

            // check token type
            $tokenType = 'next';
            // check if Link key exist in response header
            if (array_key_exists('Link', $responseHeaders)) {
                // get link text
                $link = $responseHeaders['Link'][0];
                // get next record link
                $tokenType  = strpos($link, 'rel="next') !== false ? "next" : "previous";

                $tobeReplace = ["<", ">", 'rel="next"', ";", 'rel="previous"'];

                $tobeReplaceWith = ["", "", "", ""];
                // parse url
                parse_str(parse_url(str_replace($tobeReplace, $tobeReplaceWith, $link), PHP_URL_QUERY), $op);

                $pageToken = trim($op['page_info']);
            }

            // get API rate limit
            $rateLimit = explode('/', $responseHeaders["X-Shopify-Shop-Api-Call-Limit"][0]);
            //
            $usedLimitPercentage = (100 * $rateLimit[0]) / $rateLimit[1];

            if ($usedLimitPercentage > 95) {
                // delay script for 2 second if near to api throttle
                sleep(2);
            }
            // response body
            $responseBody = json_decode($response->getBody(), true);

            // products data
            $r['products'] =  (is_array($responseBody) && count($responseBody) > 0) ? array_shift($responseBody) : $responseBody;

            $r[$tokenType]['page_token'] = isset($pageToken) ? $pageToken : null;

            return $r;
        } catch (\Exception $e) {
            return array(
                'staus'     => 'error',
                'message'   => $e->getMessage()
            );
        }
    }
}
