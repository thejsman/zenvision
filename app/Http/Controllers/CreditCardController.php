<?php

namespace App\Http\Controllers;

use Auth;

class CreditCardController extends Controller
{
    public static function getCreditCardliabilities($user = null)
    {
        if($user == null) {
          $user =  Auth::user();
        }
        $cc_accounts = $user->getCreditCardAccounts();

        $balance = 0;
        $limit = 0;
        foreach ($cc_accounts as $account) {
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

            //Change logic
            
            // if (!isset($response['error_code'])) {
            //     if (!is_null($response['liabilities']['credit'][0]['last_statement_balance'])) {
            //         $liabilities += $response['liabilities']['credit'][0]['last_statement_balance'];
            //     }
            // }

            if (!isset($response['errors'])) {
                if (!is_null($response['accounts'][0]['balances']['available'])) {
                    $balance += $response['accounts'][0]['balances']['available'];
                } else {
                    $balance += $response['accounts'][0]['balances']['current'];
                }
                $limit += $response['accounts'][0]['balances']['limit'];

            }
        }
        return $limit - $balance;
    }
}
