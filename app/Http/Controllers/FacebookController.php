<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\CustomRequests;
use App\FacebookAd;

class FacebookController extends Controller
{
    public function index(Request $request)
    {

        $code = $request->code;
        $client_id = "253783359751006";
        $client_secret = "14f0d2caacdbbf7f60dd8954bc22d900";
        $redirect_uri = "http%3A%2F%2Flocalhost%3A8000%2F";

        $query = array(
            "client_id" => $client_id, // Your API key
            "client_secret" => $client_secret, // Your app credentials (secret key)
            "code" => $code // Grab the access key from the URL
        );
        $url = "https://graph.facebook.com/v10.0/oauth/access_token?redirect_uri=" . $redirect_uri . "&client_id=" . $client_id . "&client_secret=" . $client_secret . "&code=" . $code;


        $result = CustomRequests::postRequest($url, $query);

        $access_token = $result['access_token'];

        $facebook_data = $this->getFacebookAds($access_token);


        return $facebook_data;
    }

    public function store(Request $request)
    {

        $fbdata = [];
        $fbdata['user_id'] = Auth::user()->id;
        $fbdata['ad_account_id']  = $request->id;
        $fbdata['ad_account_name']  = $request->name;
        $fbdata['access_token'] = "EAADm0IsCA14BAExKrBRiZBL3B4vxGEBcdPZBdZBMrZCu2OEoXEd8mBZBCobbNulGrw760ZCV9zgnZCA6HVMaer1CZA5gNPXLHD8rEq6qFCDucfUCR6FLFUNfMTfrqBz3NYwb5WmsPMnHPUmeP0qIZANIiZCvL3ZB1Otr3LZBNqoxVXtksAZDZD";
        $fbdata['currency'] = $request->currency;
        $result =  FacebookAd::updateOrCreate(['ad_account_id' => $fbdata['ad_account_id'], 'user_id' => Auth::user()->id], $fbdata);
    }

    public function getFacebookAds($accessToken)
    {
        $url = "https://graph.facebook.com/v10.0/me/adaccounts?access_token=" . $accessToken;

        $ads_accounts = CustomRequests::getRequest($url, "", "");

        $data_array = [];
        if (count($ads_accounts['data'])) {
            foreach ($ads_accounts['data'] as $account) {
                $account_id = $account['id'];

                $url_account_data = 'https://graph.facebook.com/v10.0/' . $account_id . '/?fields=currency,insights{spend},name&time_range{"since":2020-05-01,"until":2020-09-31}';

                $ch = curl_init();
                // return $url_account_data;
                curl_setopt($ch, CURLOPT_URL, $url_account_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


                $headers = array();
                $headers[] = 'Authorization: Bearer ' . $accessToken;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                // https://graph.facebook.com/v8.0/act_2800689140251659/?fields=currency,insights{spend},name&time_range{'since':2020-05-01,'until':2020-09-31}

                // $account_data = CustomRequests::getRequest($url_account_data, "", $accessToken);
                $response = json_decode($result, true);
                array_push($data_array, $response);
                // if (count($response['data'])) {
                //     array_push($data_array, $response['data']);
                // }
            }
        }
        return $data_array;
    }
    public function toogleAdAccount(Request $request)
    {
        $account = FacebookAd::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = FacebookAd::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
