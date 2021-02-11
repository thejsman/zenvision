<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\CustomRequests;
use App\Stripe;
use Auth;

class StripeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->getStripeAccounts();
    }

    public function store(Request $request)
    {
        $params = $request->query();
        $code = $params['code'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=" . $code);

        $headers = array();
        $headers[] = 'Authorization: Bearer sk_test_51HuhJxFYxNaGdsEt7j5ZlD9UN7ksUzcXvJy9UNvcfuf55pzIOGOm1tVho1091eW1q8Qn9wTs6oBCLdPNLSy3Z0DU00FXjCeYZR';
        // $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);



        $stripeData = [];
        $stripeData['user_id'] = Auth::user()->id;
        $stripeData['access_token']  =  $response['access_token'];
        $stripeData['refresh_token']  = $response['refresh_token'];
        $stripeData['stripe_publishable_key']  = $response['stripe_publishable_key'];
        $stripeData['stripe_user_id']  = $response['stripe_user_id'];

        $result2 =  Stripe::updateOrCreate(['user_id' => Auth::user()->id, 'stripe_user_id' => $response['stripe_user_id']], $stripeData);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return redirect('/');
    }

    public function toogleAccount(Request $request)
    {
        $account = Stripe::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = Stripe::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }

    public function getAccountBalance()
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/balance');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array(
            'Stripe-Account:' . $stripeAccounts[0]->stripe_user_id,
            'Authorization: Bearer sk_test_51HuhJxFYxNaGdsEt7j5ZlD9UN7ksUzcXvJy9UNvcfuf55pzIOGOm1tVho1091eW1q8Qn9wTs6oBCLdPNLSy3Z0DU00FXjCeYZR'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    public function getBalanceTransactions()
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/balance_transactions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array(
            'Stripe-Account:' . $stripeAccounts[0]->stripe_user_id,
            'Authorization: Bearer sk_test_51HuhJxFYxNaGdsEt7j5ZlD9UN7ksUzcXvJy9UNvcfuf55pzIOGOm1tVho1091eW1q8Qn9wTs6oBCLdPNLSy3Z0DU00FXjCeYZR'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }
}
