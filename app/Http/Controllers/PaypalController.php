<?php

namespace App\Http\Controllers;

use App\Paypal;
use Auth;
use Carbon\Carbon as Time;
use Illuminate\Http\Request;

class PaypalController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        return $user->getPaypalAccounts();
    }

    public function store(Request $request)
    {
        $params = $request->query();
        $code = $params['code'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('PAYPAL_API_URL') . 'oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=" . $code);

        $headers = array();
        $headers[] = 'Authorization: Basic ' . env('PAYPAL_BASE64_CODE');
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];
        $expires_in = $response['expires_in'];
        $paypal_account_name = $this->getPaypalAccountInfo($access_token);
        $ppData = [];
        $ppData['user_id'] = Auth::user()->id;
        $ppData['access_token'] = $access_token;
        $ppData['refresh_token'] = $refresh_token;
        $ppData['expires_at'] = date('Y/m/d H:i:s', Time::now()->timestamp + $expires_in);
        $ppData['name'] = $paypal_account_name;
        $ppData['isDeleted'] = false;
        $ppData['enabled_on_dashboard'] = true;

        Paypal::updateOrCreate(['user_id' => Auth::user()->id, 'name' => $paypal_account_name], $ppData);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return redirect('/');
    }

    public function toogleAccount(Request $request)
    {
        $account = Paypal::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = Paypal::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }

    public function getPaypalTransactions(Request $request)
    {

        $user = Auth::user();
        $paypalAccounts = $user->getPaypalAccountConnectIds();

        if ($paypalAccounts->count()) {
            foreach ($paypalAccounts as $account) {
                if (Time::now() > $account->expires_at) {
                    // generate new access token from refresh token
                    $this->getAccessToken($account);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, env('PAYPAL_API_URL') . 'reporting/transactions?start_date=' . $request->s_date . '&end_date=' . $request->e_date . '&fields=transaction_info');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $headers = array();
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Authorization: Bearer ' . $account->access_token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                $response = json_decode($result, true);
                if (array_key_exists('transaction_details', $response)) {
                    return $response['transaction_details'];
                } else {
                    return [];
                }
            }
        }
    }

    public function getPaypalDisputes(Request $request)
    {

        $user = Auth::user();
        $paypalAccounts = $user->getPaypalAccountConnectIds();

        if ($paypalAccounts->count()) {
            foreach ($paypalAccounts as $account) {
                if (Time::now() > $account->expires_at) {
                    // generate new access token from refresh token
                    $this->getAccessToken($account);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, env('PAYPAL_API_URL') . 'customer/disputes/?start_date=' . $request->s_date . '&end_date=' . $request->e_date);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $headers = array();
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Authorization: Bearer ' . $account->access_token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                $response = json_decode($result, true);
                return $response;
            }
        }
    }
    public function getAccessToken($account)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('PAYPAL_API_URL') . "identity/openidconnect/tokenservice",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=" . $account->refresh_token,
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic " . env('PAYPAL_BASE64_CODE'),
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);
        $access_token = $response['access_token'];
        $expires_in = $response['expires_in'];

        $ppData = [];
        $ppData['user_id'] = Auth::user()->id;
        $ppData['access_token'] = $access_token;

        $ppData['expires_at'] = date('Y/m/d H:i:s', Time::now()->timestamp + $expires_in);
        Paypal::updateOrCreate(['user_id' => Auth::user()->id], $ppData);

        $err = curl_error($curl);

        curl_close($curl);
    }

    public function getPaypalAccountInfo($access_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('PAYPAL_API_URL') . 'identity/oauth2/userinfo?schema=paypalv1.1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result, true);

        if (count($response)) {
            return $response['name'];
        } else {
            return null;
        }
    }
}
