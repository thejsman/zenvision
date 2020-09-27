<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ShopifyStore;
use App\Http\CustomRequests;

class ShopifyStoreController extends Controller
{
    public function getStores () {
       return Auth::user()->stores;
    }

    // validate Store url method
    public function validateUrl(Request $request) {
        $store_url = $request->store_url;
        $count = ShopifyStore::where('store_url', $store_url)->where('user_id',Auth::user()->id)->count();
        return ['count' => $count];
    }

    public function getResponse(Request $request)
    {
		    // response code from shopify
			$response_code = $request->input('code');
			// shopify store domain
			$shop_domain = $request->input('shop');
            // generating token
            $access_token = $this->getAccessToken($shop_domain, $response_code);

            ShopifyStore::create([
                'user_id'	=> Auth::user()->id,
                'store_name'=> $shop_domain,
                'store_url'	=> $shop_domain,
                'api_token'	=> $access_token
             ]);

            return redirect('/');
    }
    public function getAccessToken($shop_domain = '', $code = ''){

		$query = array(
			"client_id" => config('shopify.api_key'), // Your API key
			"client_secret" => config('shopify.api_secret'), // Your app credentials (secret key)
			"code" => $code // Grab the access key from the URL
		);

		$access_token_url = "https://" . $shop_domain . "/admin/oauth/access_token";

        //send curl request
        $result = CustomRequests::postRequest($access_token_url,$query );

        // Store the access token
        $access_token = $result['access_token'];

        return $access_token;
    }

}
