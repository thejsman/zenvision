<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon as Time;
use App\Snapchat;
use Auth;
use App\Providers\RouteServiceProvider;

class SnapchatController extends Controller
{
    public function store(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.snapchat.com/login/oauth2/access_token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&client_id=" . env('MIX_SNAPCHAT_CLIENT_ID') . "&client_secret=" . env('SNAPCHAT_SECRET_KEY') . "&code=" . $request->code . "&redirect_uri=" . env('MIX_SNAPCHAT_REDIRECT_URL'));
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $user_info = $this->getSnapchatUserInfo($response['access_token']);

        $snapchat_table_data = array(
            'user_id' =>  Auth::user()->id,
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'expires_at' => date('Y/m/d H:i:s', Time::now()->timestamp + $response['expires_in']),
            'snapchat_user_id' => $user_info['me']['id'],
            'snapchat_email' => $user_info['me']['email'],
            'organization_id' => $user_info['me']['organization_id'],
            'display_name' => $user_info['me']['display_name'],
            'member_status' => $user_info['me']['member_status'],
            'enabled_on_dashboard' => true,
            'isDeleted' => false
        );

        Snapchat::updateOrCreate([
            'user_id' =>  Auth::user()->id, 'snapchat_user_id' => $user_info['me']['id'], 'organization_id' => $user_info['me']['organization_id'],
        ], $snapchat_table_data);

        return redirect()->route('home', ['listSnapchatAccount' => $user_info['me']['organization_id']]);
    }

    protected function getSnapchatUserInfo($access_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    protected function getSnapchatAdAccounts($access_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/organizations/' . $organization_id . '/adaccounts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    public function getAdAccounts()
    {
        $user = Auth::user();
        return $user->getSnapchatAccounts();
    }

    public function listAdAccounts(Request $request)
    {
        $organization_id = $request->organization_id;
        $snapchatAccount = Snapchat::where('organization_id', $organization_id)->first();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/organizations/' . $organization_id . '/adaccounts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $snapchatAccount->access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $response['adaccounts'];
    }

    public function toogleAdAccount(Request $request)
    {
        $account = Snapchat::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = Snapchat::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
