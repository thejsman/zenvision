<?php

namespace App\Http\Controllers;

use App\GoogleAd;
use Auth;
use Carbon\Carbon as Time;
use Illuminate\Http\Request;
use Session;

class GoogleAdController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('error')) {
            if ($request->error == 'access_denied') {
                return redirect()->route('home', ['gogoleAdAccount' => 'access_denied']);
            }
        }
        $code = $request->code;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $fields_string = "";
        $fields = array(
            'client_id' => env('MIX_GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_SECRET_KEY'),
            'redirect_uri' => env('MIX_GOOGLE_REDIRECT_URL'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'access_type' => 'offline',
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        session([
            'google_access_token' => $response['access_token'],
            'google_refresh_token' => $response['refresh_token'],
            'google_expires_at' => date('Y/m/d H:i:s', Time::now()->timestamp + $response['expires_in']),
        ]);

        return redirect()->route('home', ['listGoogleAccounts' => $response['access_token']]);
    }

    public function getGoogleAdAccounts()
    {
        $user = Auth::user();
        return $user->getGoogleAccounts();
    }

    public function store(Request $request)
    {
        $google_table_data = array(
            'user_id' => Auth::user()->id,
            'ad_account_id' => str_replace('customers/', '', $request->ad_account_id),
            'access_token' => $request->session()->get('google_access_token'),
            'refresh_token' => $request->session()->get('google_refresh_token'),
            'expires_at' => $request->session()->get('google_expires_at'),
            'enabled_on_dashboard' => true,
            'isDeleted' => false,
        );

        GoogleAd::updateOrCreate([
            'user_id' => Auth::user()->id,
            'ad_account_id' => str_replace('customers/', '', $request->ad_account_id),
        ], $google_table_data);
    }

    public function toogleAdAccount(Request $request)
    {
        $account = GoogleAd::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }

    public function destroy(Request $request)
    {
        $account = GoogleAd::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }

    public function listAdAccounts(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://googleads.googleapis.com/v6/customers:listAccessibleCustomers');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $request->access_token;
        $headers[] = 'Developer-Token:' . env('GOOGLE_DEVELOPER_TOKEN');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result, true);

        if (isset($response['error'])) {
            return false;
        }
        return $response['resourceNames'];
    }

    public function getAdSpends(Request $request)
    {

        $start_date = $request->s_date;
        $end_date = $request->e_date;
        // $start_date = '2021-01-01';
        // $end_date = '2021-04-25';

        $google_ads_data = [];

        $user = Auth::user();
        $google_ad_accounts = $user->getGoogleAccounts();

        foreach ($google_ad_accounts as $account) {

            if ($account->enabled_on_dashboard) {

                if (Time::now() > $account->expires_at) {
                    // generate new access token from refresh token
                    $updated_tokens = $this->updateAccessToken($account->refresh_token);
                    $account->access_token = $updated_tokens['access_token'];

                    $account->expires_at = date('Y/m/d H:i:s', Time::now()->timestamp + $updated_tokens['expires_in']);
                    $account->save();

                    $stats = $this->fetchAdStats($updated_tokens['access_token'], $account->ad_account_id, $start_date, $end_date);

                    if (count($stats)) {
                        array_push($google_ads_data, $stats);
                    }
                } else {
                    $stats = $this->fetchAdStats($account->access_token, $account->ad_account_id, $start_date, $end_date);
                    if (count($stats)) {
                        array_push($google_ads_data, $stats);
                    }
                }
            }
        }
        return $google_ads_data;
    }

    public function updateAccessToken($refresh_token)
    {
        $data = json_encode(array(
            'client_id' => env('MIX_GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_SECRET_KEY'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        ));
        $PATH = "https://www.googleapis.com/oauth2/v4/token";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $PATH);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);
        return $response;
    }

    public function fetchAdStats($access_token, $ad_account_id, $start_date, $end_date)
    {
        $data = json_encode(array(
            'query' => 'SELECT  metrics.cost_micros , segments.date FROM customer  WHERE segments.date BETWEEN "' . $start_date . '" AND  "' . $end_date . '"',
        ));

        $PATH = 'https://googleads.googleapis.com/v6/customers/' . $ad_account_id . '/googleAds:search';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $PATH);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Developer-Token:' . env('GOOGLE_DEVELOPER_TOKEN');
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);

        if (!isset($response['error'])) {
            // close curl connection
            curl_close($ch);
            if (isset($response['results'])) {
                return $response['results'];
            } else {
                return [];
            }
        } else {

            curl_close($ch);
            return [];
        };
    }
}
