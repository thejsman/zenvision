<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Auth;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {

        $bankAccount = [];
        $bankAccount['user_id'] = Auth::user()->id;
        $bankAccount['access_token'] = $request->access_token;
        $bankAccount['bank_user'] = $request->user;
        $bankAccount['bank_name'] = $request->institution_name;
        $bankAccount['isDeleted'] = false;
        BankAccount::updateOrCreate(['user_id' => Auth::user()->id, 'bank_user' => $request->user, 'bank_name' => $request->institution_name], $bankAccount);
    }
    public function getBankAccounts()
    {
        return Auth::user()->getBankAccounts();
    }
    public function destroy(Request $request)
    {
        $account = BankAccount::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
    public function getAccountBalance()
    {
        $bank_accounts = Auth::user()->getBankAccounts();
        $balance = 0;
        foreach ($bank_accounts as $account) {
            $account_info = $this->getAccountInfo($account->access_token);
            $account_id = $account_info[0]['id'];
            $url = 'https://api.teller.io/accounts/' . $account_id . '/balances';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . base64_encode($account->access_token . ':'),
                ),
            ));

            $result = curl_exec($curl);
            $response = json_decode($result, true);
            curl_close($curl);
            if (!isset($response['errors'])) {
                // return response
                $balance += $response['available'];
            }

        }
        return $balance;
    }
    public function getAccountTransactions()
    {
        $bank_accounts = Auth::user()->getBankAccounts();
        $transactions = [];
        foreach ($bank_accounts as $account) {
            $account_info = $this->getAccountInfo($account->access_token);
            $account_id = $account_info[0]['id'];
            $url = 'https://api.teller.io/accounts/' . $account_id . '/transactions';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . base64_encode($account->access_token . ':'),
                ),
            ));

            $result = curl_exec($curl);
            $response = json_decode($result, true);
            curl_close($curl);
            if (!isset($response['errors'])) {
                // return response
                return $response;
            }

        }

    }
    public function getAccountInfo($access_token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.teller.io/accounts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($access_token . ':'),
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);
        curl_close($curl);
        return $response;

    }
    public function generateLinkToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sandbox.plaid.com/link/token/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "client_id": "' . env('PLAID_CLIENT_ID') . '",
            "secret": "' . env('PLAID_SECRET') . '",
            "client_name": "' . env('PLAID_CLIENT_NAME') . '",
            "user": { "client_user_id": "' . Auth::user()->id . '" },
            "products": ["transactions", "auth", "identity"],
            "country_codes": ["US"],
            "language": "en",
            "redirect_uri": "' . env('PLAID_REDIRECT_URI') . '"
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);
        curl_close($curl);

        if (!isset($response['errors'])) {
            // return response
            return $response['link_token'];
        } else {
            return null;
        }

    }
}
