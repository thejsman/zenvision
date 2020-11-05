<?php

namespace App\Http;
use Illuminate\Http\Request;
use App\ShopifyOrder;
use App\ShopifyOrderProduct;
use Auth;

class GetAllOrders

{
    public function getAllOrders($shop_domain, $access_token, $store_id) {

        $param = [];
        // how to get specific fields only
        $param['fields'] = 'id, order_number, name, line_items, created_at, test, total_price, total_tax, currency, financial_status, total_discounts, referring_site, landing_site, cancelled_at, total_price_usd, discount_applications, fulfillment_status, tax_lines, refunds, total_tip_received, original_total_duties_set, current_total_duties_set, shipping_address, shipping_lines';
        // order record per page
        $param['limit'] = 250;

        $param['since_id'] = 0;

        $param['access_token'] = $access_token;

       return $this->getOrderHistory($shop_domain, $param, $store_id, Auth::user()->id);
    }

    private function getOrderHistory($shop_domain = '', $param = [], $store_id = '', $user_id = '')
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

        // shopify order count api
        $orders_url = 'https://' . $shop_domain . '/admin/api/2019-10/orders.json' . '?' . http_build_query($param);

        // get order data
        $orders = $this->shopRequest('get', $orders_url);

        // check if order response exist in request
        if (isset($orders['orders'])) {
            // check if no data found
            if (!empty($orders['orders'])) {
                // iterate each order
                foreach ($orders['orders'] as $order_key => $order) {

                    // TODO: order processing here
                    $new_order = array(
                        'user_id' => $user_id,
                        'store_id' => $store_id,
                        'order_id' => $order['id'],
                        'order_number' => $order['order_number'],
                        'created_on_shopify' => $order['created_at'],
                        'test' => $order['test'],
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
                        'shipping_country' => $order['shipping_address']['country'],
                        'shipping_lines' => json_encode($order['shipping_lines']),
                    );

                   ShopifyOrder::insert($new_order);

                    // iterate each line item
                    foreach ($order['line_items'] as $line_item_key => $line_item) {

                    // TODO: line item processing here
                        $new_line_item = array (

                            'user_id' =>  Auth::user()->id,
                            'store_id' => $store_id,
                            'order_id'=> $order['id'],
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
                        //    'duties' => $line_item['duties'],
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
   return $orders;
}

private function shopRequest($method = '', $url = '') {

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
            $tokenType  = strpos($link,'rel="next') !== false ? "next" : "previous";

            $tobeReplace = ["<",">",'rel="next"',";",'rel="previous"'];

            $tobeReplaceWith = ["","","",""];
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
        $r['orders'] =  (is_array($responseBody) && count($responseBody) > 0) ? array_shift($responseBody) : $responseBody;

        $r[$tokenType]['page_token'] = isset($pageToken) ? $pageToken : null;

        return $r;
    } catch (\Exception $e) {
        return array(
            'staus'     => 'error',
            'message'   =>$e->getMessage()
        );
    }
}
}
