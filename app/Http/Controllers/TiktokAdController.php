<?php

namespace App\Http\Controllers;

use App\TiktokAd;
use Auth;
use Illuminate\Http\Request;

class TiktokAdController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        return $user->getTiktokAccounts();
    }
    public function store(Request $request)
    {
        $params = $request->query();
        $auth_code = $params['auth_code'];
        $data = json_encode(array(
            "secret" => env('TIKTOK_SECRET_KEY'),
            "app_id" => env('MIX_TIKTOK_APP_ID'),
            "auth_code" => $auth_code,
        ));
        $PATH = "https://ads.tiktok.com/open_api/oauth2/access_token_v2/";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $PATH);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        //Windows Dev system disable SSL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        if ($response) {
            $access_token = $response['data']['access_token'];
            $account_info = $this->getTiktokAccountInfo($access_token);

            $tiktok_data = [];
            $tiktok_data['user_id'] = Auth::user()->id;
            $tiktok_data['access_token'] = $access_token;
            $tiktok_data['display_name'] = $account_info['display_name'];
            $tiktok_data['tiktok_id'] = $account_info['id'];
            $tiktok_data['tiktok_email'] = $account_info['email'];
            $tiktok_data['isDeleted'] = false;
            $tiktok_data['enabled_on_dashboard'] = true;

            TiktokAd::updateOrCreate(['user_id' => Auth::user()->id, 'tiktok_id' => $account_info['id']], $tiktok_data);
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return redirect('/');
    }

    public function getTiktokAccountInfo($access_token)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ads.tiktok.com/open_api/v1.2/user/info/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Access-Token:' . $access_token,
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);

        $response = json_decode($result, true);

        if ($response) {
            return $response['data'];
        } else {
            return null;
        }
    }
    public function toogleAccount(Request $request)
    {
        $account = TiktokAd::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = TiktokAd::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
