<?php

namespace App\Http\Controllers;

use App\StripeAccount;
use Auth;
use Illuminate\Http\Request;
use Stripe;

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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . env('STRIPE_ACCESS_TOKEN');
        //Windows Dev system disable SSL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);

        if ($response) {

            $account_name = $this->getStripeAccountInfo($response['stripe_user_id'], $response['access_token']);
            $stripeData = [];
            $stripeData['user_id'] = Auth::user()->id;
            $stripeData['access_token'] = $response['access_token'];
            $stripeData['refresh_token'] = $response['refresh_token'];
            $stripeData['stripe_publishable_key'] = $response['stripe_publishable_key'];
            $stripeData['stripe_user_id'] = $response['stripe_user_id'];
            $stripeData['name'] = $account_name;
            $stripeData['isDeleted'] = false;
            $stripeData['enabled_on_dashboard'] = true;

            DB::table('stripe')->updateOrCreate(['user_id' => Auth::user()->id, 'stripe_user_id' => $response['stripe_user_id']], $stripeData);
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return redirect('/');
    }

    public function toogleAccount(Request $request)
    {
        $account = StripeAccount::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }
    public function destroy(Request $request)
    {
        $account = StripeAccount::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }

    public function getAccountBalance()
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $availableBalance = [];
        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/balance');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                //Windows Dev system disable SSL
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $headers = array(
                    'Stripe-Account:' . $account->stripe_user_id,
                    'Authorization: Bearer ' . $account->access_token,
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    return [];
                }
                curl_close($ch);
                $response = json_decode($result, true);

                array_push($availableBalance, $response['available'][0]);
            }
            return $availableBalance;
        } else {
            return $availableBalance;
        }
    }

    public function getStripeTransactionsSdk(Request $request)
    {

        $balance_transaction = [];
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripeTransactions = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $stripe = new \Stripe\StripeClient(
                    $account->access_token
                );
                $transactions = $stripe->balanceTransactions->all(['limit' => 100, 'created' => array(
                    'gte' => strtotime(date_format(date_create($request->s_date), 'Y/d/m')),
                    'lte' => strtotime(date_format(date_create($request->e_date), 'Y/d/m') . " +1 days"),
                )]);

                foreach ($transactions->autoPagingIterator() as $transaction) {
                    if ($transaction->fee > 0) {
                        array_push($balance_transaction, array('created' => $transaction->created, 'fee' => $transaction->fee));
                    }

                }
            }

            return $balance_transaction;
        } else {

            return $balance_transaction;
        }

    }

    public function getStripeTransactions()
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripeTransactions = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/balance_transactions');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_POSTFIELDS, "limit=100");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = array(
                    'Stripe-Account:' . $account->stripe_user_id,
                    'Authorization: Bearer ' . $account->access_token,
                );

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    return [];
                }
                curl_close($ch);

                $response = json_decode($result, true);

                if (count($response['data'])) {
                    array_push($stripeTransactions, $response['data']);
                }
            }
            return ['stripeTransactions' => $stripeTransactions];
        }
    }

    public function getStripeAccountInfo($account_id, $access_token)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/accounts/' . $account_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Stripe-Account:' . $account_id,
            'Authorization: Bearer ' . $access_token,
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
            return $response['business_profile']['name'];
        } else {
            return null;
        }
    }

    public function getStripeChargebacksSdk(Request $request)
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripe_chargebacks = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $stripe = new \Stripe\StripeClient(
                    $account->access_token
                );
                $disputes = $stripe->disputes->all(['limit' => 100, 'created' => array(
                    'gte' => strtotime(date_format(date_create($request->s_date), 'Y/d/m')),
                    'lte' => strtotime(date_format(date_create($request->e_date), 'Y/d/m') . " +1 days"),
                )]);

                foreach ($disputes->autoPagingIterator() as $dispute) {
                    array_push($stripe_chargebacks, array('created' => $dispute->created, 'amount' => $dispute->fee, 'status' => $dispute->status, 'currency' => $dispute->currency));
                }
            }

            return $stripe_chargebacks;
        }
    }

    public function getStripeChargebacks()
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripe_chargebacks = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/disputes');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_POSTFIELDS, "limit=100");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = array(
                    'Stripe-Account:' . $account->stripe_user_id,
                    'Authorization: Bearer ' . $account->access_token,
                );

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    return [];
                }
                curl_close($ch);

                $response = json_decode($result, true);

                if (count($response['data'])) {
                    array_push($stripe_chargebacks, $response['data']);
                }
            }
        }
        return ['stripeChargebacks' => $stripe_chargebacks];
    }
}
