<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {

        $bankAccount = [];
        $bankAccount['user_id'] = Auth::user()->id;
        $bankAccount['access_token'] = $this->getAccessToken($request->public_token);
        $bankAccount['bank_user_id'] = $request->id;
        $bankAccount['bank_user_name'] = $request->name;
        $bankAccount['bank_type'] = $request->type;
        $bankAccount['bank_subtype'] = $request->subtype;
        $bankAccount['bank_name'] = $request->institution_name;
        $bankAccount['institution_id'] = $request->institution_id;
        $bankAccount['isDeleted'] = false;

        BankAccount::updateOrCreate(['user_id' => Auth::user()->id, 'bank_user_id' => $request->user, 'bank_user_name' => $request->institution_name], $bankAccount);

        // Save Bank Logo
        $blob = $this->getBankLogo($request->institution_id);
        $this->createImageFromBase64($blob, $request->institution_id);

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
          if($account->bank_type == "depository") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sandbox.plaid.com/accounts/balance/get',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "client_id": "' . env('PLAID_CLIENT_ID') . '",
                "secret": "' . env('PLAID_SECRET') . '",
                "access_token": "' . $account->access_token . '",
                "options": {
                  "account_ids": ["' . $account->bank_user_id . '"]
                }
              }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));

            $result = curl_exec($curl);
            $response = json_decode($result, true);
            curl_close($curl);
            if (!isset($response['errors'])) {
                if (!is_null($response['accounts'][0]['balances']['available'])) {
                    $balance += $response['accounts'][0]['balances']['available'];
                } else {
                    $balance += $response['accounts'][0]['balances']['current'];
                }
            }
          }          
        }
        return $balance;
    }
    public function getAccountTransactions(Request $request)
    {
        $bank_accounts = Auth::user()->getBankAccounts();
        $transactions = [];

        foreach ($bank_accounts as $account) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sandbox.plaid.com/transactions/get',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => '{
                    "client_id": "' . env('PLAID_CLIENT_ID') . '",
                    "secret": "' . env('PLAID_SECRET') . '",
                    "access_token": "' . $account->access_token . '",
                    "options": {
                        "account_ids": ["' . $account->bank_user_id . '"],
                        "count": 500,
                        "offset": 0
                    },
                    "start_date": "' . $request->start_date . '",
                    "end_date": "' . $request->end_date . '"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result, true);

            $transactions = array_merge($transactions, $response['transactions']);
        }
        return $transactions;
    }
    public function createImageFromBase64($blob, $name)
    {
        $file_name = $name . '.png';
        if ($blob != "") {
            Storage::disk('bank_icons')->put($file_name, base64_decode($blob));
        }
    }

    public function getBankLogo($institution_id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sandbox.plaid.com/institutions/get_by_id',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "institution_id": "' . $institution_id . '",
            "client_id": "' . env('PLAID_CLIENT_ID') . '",
            "secret": "' . env('PLAID_SECRET') . '",
            "country_codes": ["US"],
            "options" : {
                    "include_optional_metadata" : true
                 }
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);

        curl_close($curl);

        if (!isset($response['errors'])) {

            return $response['institution']['logo'];
        } else {
            return null;
        }
    }
    // Plaid

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
            "products": ["transactions"],
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
    protected function getAccessToken($public_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sandbox.plaid.com/item/public_token/exchange',
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
            "public_token": "' . $public_token . '"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result, true);
        curl_close($curl);
        return $response['access_token'];
    }
}
