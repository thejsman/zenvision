<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ShopifyStore;

class ShopifyStoreController extends Controller
{
    public function getStores () {
       return Auth::user()->stores;
    }

    public function getPermission()
	{
		$this->foo = Shopify::make($this->shop, $this->scopes);
		return $this->foo->redirect();
	}

    public function getResponse(Request $request)

    {

			// $this->getPermission();
			// response code from shopify
			$response_code = $request->input('code');
			// shopify store domain
			$shop_domain = $request->input('shop');
            // generating token
            $store_db_data = array(
                'user_id'	=> Auth::user()->id,
                'store_name'=> $shop_domain,
                'store_url'	=> $shop_domain,
                'api_token'	=> $response_code,
         );
         ShopifyStore::create($store_db_data);
         return redirect('/');
    }
    public function getAccessToken($shop_domain = '', $code = ''){

		$query = array(
			"client_id" => config('6475dbe1c3d0b763d819fc4d053d771e'), // Your API key
			"client_secret" => config('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'), // Your app credentials (secret key)
			"code" => $code // Grab the access key from the URL
		);

		$access_token_url = "https://" . $shop_domain . "/admin/oauth/access_token";

		// Configure curl client and execute request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $access_token_url);
            curl_setopt($ch, CURLOPT_POST, count($query));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
            $result = curl_exec($ch);
            curl_close($ch);

            // Store the access token
            $result = json_decode($result, true);
            $access_token = $result['access_token'];

		return $access_token;
    }
    public function getStoreUser () {
       return ShopifyStore::first()->user;
    }
}
