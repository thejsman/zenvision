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

    public function tiktokConnect(Request $request)
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

        $headers = array();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);

        if (sizeof($response['data']['advertiser_ids'])) {

            session([
                'tiktok_access_token' => $response['data']['access_token'],
                //Zendrop Tiktok Token
                // 'tiktok_access_token' => "ed86881c96e3f73954f81e32f26a82e70b2d6cdb",

            ]);
            return redirect()->route('home', ['listTiktokAccounts' => $response['data']['access_token']]);
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return redirect()->route('home', ['listTiktokAccounts' => 'noaccount']);

        //    Redirect with Zendrop Tiktok Access Token
        // session([
        //     'tiktok_access_token' => "ed86881c96e3f73954f81e32f26a82e70b2d6cdb",
        // ]);
        // return redirect()->route('home', ['listTiktokAccounts' => "ed86881c96e3f73954f81e32f26a82e70b2d6cdb"]);
    }

    public function store(Request $request)
    {

        $tiktok_data = [];
        $tiktok_data['user_id'] = Auth::user()->id;
        $tiktok_data['access_token'] = $request->session()->get('tiktok_access_token');
        $tiktok_data['advertiser_name'] = $request->advertiser_name;
        $tiktok_data['advertiser_id'] = $request->advertiser_id;
        $tiktok_data['isDeleted'] = false;
        $tiktok_data['enabled_on_dashboard'] = true;

        TiktokAd::updateOrCreate(['user_id' => Auth::user()->id, 'advertiser_id' => $request->advertiser_id], $tiktok_data);
    }

    public function getTiktokAccountInfo(Request $request)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ads.tiktok.com/open_api/v1.2/oauth2/advertiser/get/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $fields = array(
            'access_token' => $request->access_token,
            'app_id' => env('MIX_TIKTOK_APP_ID'),
            'secret' => env('TIKTOK_SECRET_KEY'),
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

        $result = curl_exec($ch);
        // return $result;
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);

        $response = json_decode($result, true);

        $castedArray = [];

        if (isset($response['data']['list'])) {
            foreach ($response['data']['list'] as $data) {
                array_push($castedArray, ['advertiser_id' => (string) $data['advertiser_id'], 'advertiser_name' => $data['advertiser_name']]);
            }
            return $castedArray;
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

    public function getTiktokAdSpend(Request $request)
    {

        $user = Auth::user();
        $tiktokAccounts = $user->getTiktokAccounts();

        if ($tiktokAccounts->count()) {
            foreach ($tiktokAccounts as $account) {

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://ads.tiktok.com/open_api/v1.1/reports/advertiser/get/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,

                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS => 'advertiser_id=' . $account->advertiser_id . '&start_date=' . $request->s_date . '&end_date=' . $request->e_date . '&page_size=100&time_granularity=STAT_TIME_GRANULARITY_DAILY',
                    CURLOPT_HTTPHEADER => array(
                        'Access-Token: ' . $account->access_token,
                        'Content-Type: application/x-www-form-urlencoded',
                    ),
                ));

                $result = curl_exec($curl);

                curl_close($curl);
                $response = json_decode($result, true);

                if (isset($response['data']['list'])) {
                    return $response['data']['list'];
                } else {
                    return [];
                }
            }
        }
    }
}
