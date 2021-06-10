<?php

namespace App\Http\Controllers;

use App\Snapchat;
use App\SnapchatAdAccount;
use Auth;
use Carbon\Carbon as Time;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class SnapchatAdAccountController extends Controller
{
    public function store(Request $request)
    {
        $snapchatAccount = Snapchat::where('organization_id', $request->organization_id)->first();
        $ad_account = array(
            'user_id' => $snapchatAccount->user_id,
            'snapchat_user_id' => $snapchatAccount->snapchat_user_id,
            'ad_account_id' => $request->id,
            'ad_account_name' => $request->name,
            'organization_id' => $request->organization_id,
            'access_token' => $snapchatAccount->access_token,
            'refresh_token' => $snapchatAccount->refresh_token,
            'expires_at' => $snapchatAccount->expires_at,
            'isDeleted' => false,
            'enabled_on_dashboard' => true,
            'type' => $request->type,
            'currency' => $request->currency,
            'status' => $request->status,
            'timezone' => $request->timezone,
        );

        SnapchatAdAccount::create($ad_account);
    }

    public function updateAccessToken($refresh_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.snapchat.com/login/oauth2/access_token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "refresh_token=" . $refresh_token . "&client_id=" . env('MIX_SNAPCHAT_CLIENT_ID') . "&client_secret=" . env('SNAPCHAT_SECRET_KEY') . "&grant_type=refresh_token");

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

    public function getAdSpend(Request $request)
    {

        $user = Auth::user();
        $snapchatAccounts = $user->getSnapchatAccounts();
        $snapchatStats = [];

        if ($snapchatAccounts->count()) {
            foreach ($snapchatAccounts as $account) {
                if ($account->enabled_on_dashboard) {

                    $start_date = $this->convertTimeToLocal($request->s_date, $account->timezone);
                    $end_date = $this->convertTimeToLocal($request->e_date, $account->timezone);

                    if (Time::now() > $account->expires_at) {
                        // generate new access token from refresh token
                        $updated_tokens = $this->updateAccessToken($account->refresh_token);

                        $account->access_token = $updated_tokens['access_token'];
                        $account->refresh_token = $updated_tokens['refresh_token'];
                        $account->expires_at = date('Y/m/d H:i:s', Time::now()->timestamp + $updated_tokens['expires_in']);
                        $account->save();

                        $stats = $this->fetchAdStats($updated_tokens['access_token'], $account->ad_account_id, $start_date, $end_date);
                        array_push($snapchatStats, $stats);

                    } else {
                        $stats = $this->fetchAdStats($account->access_token, $account->ad_account_id, $start_date, $end_date);
                        array_push($snapchatStats, $stats);
                    }
                }
            }
        }
        return $snapchatStats;
    }
    public function fetchAdStats($access_token, $ad_account_id, $start_date, $end_date)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://adsapi.snapchat.com/v1/adaccounts/' . $ad_account_id . '/stats?granularity=DAY&start_time=' . urlencode($start_date) . '&end_time=' . urlencode($end_date),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);
        curl_close($curl);
        return $response['timeseries_stats'][0]['timeseries_stat']['timeseries'];
    }

    public function convertTimeToLocal($datetime, $timezone = 'Europe/Zurich')
    {
        $given = new DateTime($datetime, new DateTimeZone($timezone));
        $given->setTimezone(new DateTimeZone($timezone));
        $output = $given->format("c");
        return $output;
    }
}
