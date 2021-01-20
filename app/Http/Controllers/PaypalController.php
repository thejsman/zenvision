<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\CustomRequests;
use App\Paypal;
use Auth;

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

        curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=" . $code);

        $headers = array();
        $headers[] = 'Authorization: Basic QWRaSXQ1MGk1aUFWVDVWNjg4eUdtZ20tWkhBeUx5bmNZME5sSVBXOVk0emtRT2ZzYkoybTQtN0JKYTdVNkVlR2l2QjA5WG51LTV4TGpzMko6RUotLXFaWHV6dlVJZ05UdDkzZXlKYUoyY2QxYloyZXhqMUZabGNOS21idjhnODVuYkR3XzdiR0dSMVRvUVRhTTNRMFptdnF4RzBDYTlacHM=';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        $access_token = $response['access_token'];
        $refresh_token  = $response['refresh_token'];


        $ppData = [];
        $ppData['user_id'] = Auth::user()->id;
        $ppData['access_token']  = $access_token;
        $ppData['refresh_token']  = $refresh_token;

        $result2 =  Paypal::updateOrCreate(['user_id' => Auth::user()->id], $ppData);

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

    public function getPaypalTransactions()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/reporting/transactions?start_date=2020-12-01T00:00:00-0700&end_date=2020-12-30T23:59:59-0700&fields=transaction_info');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer A23AAIDatwQvYPQ2r0IMtaMDNy6Lk7KBEoWWqh15yB_fYWJy7-gh8YSs3lDQlAk2-sWXxxHf0vdyKVZ9rLU59wivMvcIkwBZQ';
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
