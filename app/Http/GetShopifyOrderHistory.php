<?php


// from another function:

// initialize variable
$param = [];
// how to get specific fields only
$param['fields'] = 'id, name, line_items, created_at';
// order record per page
$param['limit'] = 250;

$param['since_id'] = // TODO: entire history

$param['access_token'] = // TODO: access token of store

$this->getOrderHistory($shop_domain, $param, $store_id, $user_id);

/**
 *
 * @param string $shop_domain
 * @param array $param
 * @param string $store_id
 * @param string $user_id
 */
private function getOrderHistory ($shop_domain = '', $param = [], $store_id = '', $user_id = '')
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

                    // iterate each line item
                    foreach ($order['line_items'] as $line_item_key => $line_item) {

                    // TODO: line item processing here


                    }
                }

            } else {
                break;
            }
        }

        // next page token
        $nextPageToken = $orders['next']['page_token'] ?? null;

    } while ($nextPageToken != null);

}

/**
 * shopify api request
 *
 * @param string $method
 * @param string $url
 * @return array
 */
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
