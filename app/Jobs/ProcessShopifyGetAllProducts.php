<?php

namespace App\Jobs;

use App\ShopifyProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessShopifyGetAllProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shop_domain;
    public $param;
    public $store_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop_domain, $param, $store_id)
    {
        $this->shop_domain = $shop_domain;
        $this->param = $param;
        $this->store_id = $store_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // initialize variable
        $nextPageToken = null;

        do {
            // check if next page token is not null
            if ($nextPageToken != null) {
                // remove since id if page info parameter set
                unset($this->param['since_id']);
            }
            // next page token
            $this->param['page_info'] = $nextPageToken;

            // shopify product count api
            $products_url = 'https://' . $this->shop_domain . '/admin/api/2020-07/products.json' . '?' . http_build_query($this->param);

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
                            'store_id' => $this->store_id,
                            'product_id' => $product['id'],
                            'product_title' => $product['title'],
                        );
                        foreach ($product['variants'] as $variant_key => $variant) {

                            $inventory_url = 'https://' . $this->shop_domain . '/admin/api/2021-01/inventory_items.json?ids=' . $variant['inventory_item_id'] . '?' . http_build_query($this->param);
                            $inventory = $this->shopRequest('get', $inventory_url);
                            $cost = $inventory['products'][0]['cost'];
                            $product_variant = array(
                                'variant_id' => $variant['id'],
                                // 'variant_title' => $variant['title'],
                                'variant_title' => "title from queue",
                                'sku' => $variant['sku'] ? $variant['sku'] : 'no_sku',
                                'sales_price' => $variant['price'],
                                'inventory_item_id' => $variant['inventory_item_id'],
                                'color' => isset($colorOption) ? $variant['option' . $colorOption] : null,
                                "size" => isset($sizeOption) ? $variant['option' . $sizeOption] : null,
                                'cost' => $cost,
                                // "shipping_cost" => 0
                            );
                            $newProduct = array_merge($new_product, $product_variant);

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
                    'Accept' => 'application/json',
                ],
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
                $tokenType = strpos($link, 'rel="next') !== false ? "next" : "previous";

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
            $r['products'] = (is_array($responseBody) && count($responseBody) > 0) ? array_shift($responseBody) : $responseBody;

            $r[$tokenType]['page_token'] = isset($pageToken) ? $pageToken : null;

            return $r;
        } catch (\Exception $e) {
            return array(
                'staus' => 'error',
                'message' => $e->getMessage(),
            );
        }
    }
}
