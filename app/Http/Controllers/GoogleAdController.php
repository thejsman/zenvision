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
        $code = $request->code;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $fields_string = "";
        $fields = array(
            'client_id' => env('MIX_GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_SECRET_KEY'),
            'redirect_uri' => env('MIX_GOOGLE_REDIRECT_URL'),
            'grant_type' => 'authorization_code',
            'code' => $code,
        );
        // foreach ($fields as $key => $value) {
        //     $fields_string .= $key . '=' . $value . '&';
        // }
        // rtrim($fields_string, '&');

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
        return $response;
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
        return $response['resourceNames'];
    }
}
