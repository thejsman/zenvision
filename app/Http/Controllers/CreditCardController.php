<?php

namespace App\Http\Controllers;

use Auth;

class CreditCardController extends Controller
{
    public function getCreditCardliabilities()
    {
        $cc_accounts = Auth::user()->getCreditCardAccounts();

        $liabilities = 0;
        foreach ($cc_accounts as $account) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://sandbox.plaid.com/liabilities/get',
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
                if (!is_null($response['liabilities']['credit'][0]['last_statement_balance'])) {
                    $liabilities += $response['liabilities']['credit'][0]['last_statement_balance'];
                }
            }
        }
        return $liabilities;
    }
}
