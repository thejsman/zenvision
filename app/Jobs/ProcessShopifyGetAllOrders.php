<?php

namespace App\Jobs;

use App\ShopifyOrder;
use App\ShopifyOrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Cache;


class ProcessShopifyGetAllOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shop_domain;
    public $param;
    public $store_id;
    public $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop_domain, $param, $store_id, $user_id)
    {
        $this->shop_domain = $shop_domain;
        $this->param = $param;
        $this->store_id = $store_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nextPageToken = null;

        do {
            // check if next page token is not null
            if ($nextPageToken != null) {
                // remove since id if page info parameter set
                unset($this->param['since_id']);
            }
            // next page token
            $this->param['page_info'] = $nextPageToken;

            // shopify order count api
            $orders_url = 'https://' . $this->shop_domain . '/admin/api/2019-10/orders.json' . '?' . http_build_query($this->param);

            // get order data
            $orders = $this->shopRequest('get', $orders_url);

            // Log::info($orders);
            // check if order response exist in request
            if (isset($orders['orders'])) {
                // check if no data found
                if (!empty($orders['orders'])) {
                    // iterate each order
                    foreach ($orders['orders'] as $order_key => $order) {
                        $new_order = array(
                            'user_id' => $this->user_id,
                            'store_id' => $this->store_id,
                            'order_id' => $order['id'],
                            'order_number' => $order['order_number'],
                            'created_on_shopify' => $order['created_at'],
                            'total_price' => $order['total_price'],
                            'total_tax' => $order['total_tax'],
                            'currency' => $order['currency'],
                            'financial_status' => $order['financial_status'],
                            'total_discounts' => $order['total_discounts'],
                            'referring_site' => $order['referring_site'],
                            'landing_site' => $order['landing_site'],
                            'cancelled_at' => $order['cancelled_at'],
                            'total_price_usd' => $order['total_price_usd'],
                            'discount_applications' => json_encode($order['discount_applications']),
                            'fulfillment_status' => $order['fulfillment_status'],
                            'tax_lines' => json_encode($order['tax_lines']),
                            'refunds' => json_encode($order['refunds']),
                            'total_tip_received' => $order['total_tip_received'],
                            'shipping_country' => array_key_exists('shipping_address', $order) ? $order['shipping_address']['country'] : null,
                            'shipping_lines' => json_encode($order['shipping_lines']),
                        );

                        ShopifyOrder::insert($new_order);

                        // iterate each line item
                        foreach ($order['line_items'] as $line_item_key => $line_item) {
                            $new_line_item = array(
                                'user_id' => $this->user_id,
                                'store_id' => $this->store_id,
                                'order_id' => $order['id'],
                                'order_number' => $order['order_number'],
                                'line_item_id' => $line_item['id'],
                                'variant_id' => $line_item['variant_id'],
                                'title' => $line_item['title'],
                                'quantity' => $line_item['quantity'],
                                'sku' => $line_item['sku'],
                                'variant_title' => $line_item['variant_title'],
                                'fulfillment_service' => $line_item['fulfillment_service'],
                                'product_id' => $line_item['product_id'],
                                'price' => $line_item['price'],
                                'total_discount' => $line_item['total_discount'],
                                'fulfillment_status' => $line_item['fulfillment_status'],
                                // 'duties' => $line_item['duties'],
                                'tax_lines' => $line_item['tax_lines'],
                            );
                            ShopifyOrderProduct::create($new_line_item);
                        }
                    }
                } else {
                    break;
                }
            }

            // next page token
            $nextPageToken = $orders['next']['page_token'] ?? null;
        } while ($nextPageToken != null);
        //Cache Clear
        Cache::tags(['SHOPIFY:' . $this->user_id])->flush();
    }
    private function shopRequest($method = '', $url = '')
    {
        try {
            // http client object
            $client = new \GuzzleHttp\Client(['verify' => false]);
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
            // order data
            $r['orders'] = (is_array($responseBody) && count($responseBody) > 0) ? array_shift($responseBody) : $responseBody;

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
