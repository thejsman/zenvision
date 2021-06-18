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
        $bankAccount['bank_user_id'] = $request->id;
        $bankAccount['bank_user_name'] = $request->name;
        $bankAccount['bank_type'] = $request->type;
        $bankAccount['bank_subtype'] = $request->subtype;
        $bankAccount['bank_name'] = $request->institution_name;
        $bankAccount['isDeleted'] = false;
        BankAccount::updateOrCreate(['user_id' => Auth::user()->id, 'bank_user_id' => $request->user, 'bank_user_name' => $request->institution_name], $bankAccount);
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
}
