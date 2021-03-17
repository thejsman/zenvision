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
        $redirect_uri = "https%3A%2F%2Fstaging.zenvision.io%2F";

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

    public function getAdAccounts()
    {
        $user = Auth::user();
        return $user->getFacebookAccounts();
    }

    public function store(Request $request)
    {

        $fbdata = [];
        $fbdata['user_id'] = Auth::user()->id;
        $fbdata['ad_account_id']  = $request->id;
        $fbdata['ad_account_name']  = $request->name;
        $fbdata['access_token'] = $request->access_token;
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

                $url_account_data = 'https://graph.facebook.com/v10.0/' . $account_id . '/?fields=currency,insights{spend},name&time_range{"since":2021-02-17,"until":2021-03-17}';

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
                $response = json_decode($result, true);
                $response['access_token'] = $accessToken;
                array_push($data_array, $response);
            }
        }
        return $data_array;
    }
    public function getFacebookAdsData(Request $request)
    {
        $fb_ads_data = [];
        $user = Auth::user();
        $fb_ad_accounts =  $user->getFacebookAccounts();
        foreach ($fb_ad_accounts as $fb_ad_account) {

            // $url = 'https://graph.facebook.com/v10.0/' . $fb_ad_account->ad_account_id . '/insights?&time_interval={since:' . $request->s_date . ',until:' . $request->e_date . '}&time_increment=1&access_token=' . $fb_ad_account->access_token;
            $url = 'https://graph.facebook.com/v10.0/' . $fb_ad_account->ad_account_id . '/insights?&time_interval={since:2021-02-17,until:2021-03-17}&time_increment=1&access_token=' . $fb_ad_account->access_token;
            $spend =  CustomRequests::getRequest($url, '', '');
            if (count($spend['data']) > 0) {
                array_push($fb_ads_data, $spend['data']);
            }
        }
        return  $fb_ads_data;
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
