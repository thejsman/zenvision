<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SnapchatAdAccount;
use App\Snapchat;
use Auth;

class SnapchatAdAccountController extends Controller
{
    public function store(Request $request)
    {
        $snapchatAccount = Snapchat::where('organization_id', $request->organization_id)->first();
        $ad_account = array(
            'user_id' => $snapchatAccount->user_id,
            'snapchat_user_id' => $snapchatAccount->snapchat_user_id,
            'ad_account_id'  => $request->id,
            'ad_account_name'  => $request->name,
            'organization_id' => $request->organization_id,
            'access_token' => $snapchatAccount->access_token,
            'refresh_token' => $snapchatAccount->refresh_token,
            'expires_at' => $snapchatAccount->expires_at,
            'isDeleted' => false,
            'enabled_on_dashboard' => true,
            'type' => $request->type,
            'currency' => $request->currency,
            'status' => $request->status,
            'timezone' => $request->timezone
        );

        SnapchatAdAccount::create($ad_account);
    }

    public function updateAccessToken(Request $request)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.snapchat.com/login/oauth2/access_token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "refresh_token=" . $request->refresh_token . "&client_id=" . env('MIX_SNAPCHAT_CLIENT_ID') . "&client_secret=" . env('SNAPCHAT_SECRET_KEY') . "&grant_type=refresh_token");

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    public function toogleAdAccount(Request $request)
    {
        $account = SnapchatAdAccount::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = SnapchatAdAccount::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
