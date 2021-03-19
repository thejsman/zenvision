<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SnapchatAdAccount;
use Auth;

class SnapchatAdAccountController extends Controller
{
    public function store(Request $request)
    {
        $ad_account = array(
            'user_id' => Auth::user()->id,
            'ad_account_id'  => $request->id,
            'ad_account_name'  => $request->name,
            'organization_id' => $request->organization_id,
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
}
