<?php

namespace App\Http\Controllers;


use App\Jobs\ProcessStripeCsvReport;
use App\StripeAccount;
use App\StripeBalanceTransactionsReport;
use App\StripeReport;
use Auth;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Cache;

class StripeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->getStripeAccounts();
    }
    public function getStripeAccountsForPA()
    {
        $user = Auth::user();
        return $user->getStripeAccounts();
    }
    public function store(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/');
        }
        $params = $request->query();
        $code = $params['code'];
        $state = $params['state'];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=" . $code);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . env('STRIPE_ACCESS_TOKEN');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $response = json_decode($result, true);

        if ($response) {
            $account_name = $this->getStripeAccountInfo($response['stripe_user_id'], $response['access_token'])['name'];
            $time_zone = $this->getStripeAccountInfo($response['stripe_user_id'], $response['access_token'])['time_zone'];
            $stripeData = [];
            $stripeData['user_id'] = Auth::user()->id;
            $stripeData['access_token'] = $response['access_token'];
            $stripeData['refresh_token'] = $response['refresh_token'];
            $stripeData['stripe_publishable_key'] = $response['stripe_publishable_key'];
            $stripeData['stripe_user_id'] = $response['stripe_user_id'];
            $stripeData['name'] = $account_name;
            $stripeData['isDeleted'] = false;
            $stripeData['enabled_on_dashboard'] = true;
            $stripeData['report_status'] = false;
            $stripeData['time_zone'] = $time_zone;

            $object = StripeAccount::updateOrCreate(['user_id' => Auth::user()->id, 'stripe_user_id' => $response['stripe_user_id']], $stripeData);

            $this->createReportRun($response['access_token']);

            if (strpos($state, 'mastersheet') !== false) {
                return redirect()->route('mastersheet', ['stripeAddAccount' => 'success', 'record_id' => $object->id]);
            } else {
                return redirect()->route('home', ['stripeAddAccount' => 'success', 'record_id' => $object->id]);
            }
        } else {
            if (strpos($state, 'mastersheet') !== false) {
                return redirect()->route('mastersheet', ['stripeAddAccount' => 'error']);
            } else {
                return redirect()->route('home', ['stripeAddAccount' => 'error']);
            }
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        // $this->createReportWebhook($response['access_token']);

    }

    public function getMerchantFees(Request $request)
    {

        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripeTransactions = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                // return [new DateTime($request->s_date, new DateTimeZone($account->time_zone)), new DateTime($request->e_date, new DateTimeZone($account->time_zone))];
                if ($account->enabled_on_dashboard) {
                    $stripeAccountTransactions = StripeBalanceTransactionsReport::where('user_id', $account->user_id)->where('stripe_user_id', $account->stripe_user_id)->whereBetween('created', [new DateTime($request->s_date, new DateTimeZone($account->time_zone)), new DateTime($request->e_date, new DateTimeZone($account->time_zone))])->select('created', 'fee')->get()->toArray();
                    $stripeTransactions = array_merge($stripeTransactions, $stripeAccountTransactions);
                }
            }
        }
        return $stripeTransactions;
    }

    public function getStripeTransactionsFromDb(Request $request)
    {
        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripeTransactions = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $stripeTransactions = StripeBalanceTransactionsReport::where('user_id', $account->user_id)->where('stripe_user_id', $account->stripe_user_id)->orderBy('created', 'desc')->paginate(20);
                return $stripeTransactions;
            }
        }
        return ['stripeTransactions' => $stripeTransactions];
    }
    public function getStripeTransactionsDateWise(Request $request)
    {

        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripeTransactions = [];

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $stripeAccountTransactions = StripeBalanceTransactionsReport::where('user_id', $account->user_id)->where('stripe_user_id', $account->stripe_user_id)->whereBetween('created', [$request->start_date, $request->end_date])->orderBy('created', 'desc')->get()->toArray();
                $stripeTransactions = array_merge($stripeTransactions, $stripeAccountTransactions);
            }
        }
        return $stripeTransactions;
    }
    public function toogleAccount(Request $request)
    {
        $account = StripeAccount::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;

        $account->save();

        //Cache Clear
        Cache::tags(['STRIPE:' . Auth::user()->id])->flush();
    }
    public function destroy(Request $request)
    {
        $account = StripeAccount::find($request->id);
        $account->isDeleted = true;
        $account->save();
        //Cache Clear
        Cache::tags(['STRIPE:' . Auth::user()->id])->flush();
    }

    public function getReportStatus(Request $request)
    {
        $stripe_record = StripeAccount::find($request->record_id)->select('report_status')->first();
        return $stripe_record;
    }

    public function getReportStatus2(Request $request)
    {

        $stripe_record =  StripeReport::where('user_id', Auth::user()->id)->where('stripe_user_id', $request->stripe_user_id)->where('start_date', $request->s_date)->where('end_date', $request->e_date)->where('report_status', true)->first();
        return $stripe_record;
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

    public function createReportRun($access_token)
    {
        $today = strtotime(date("Y/m/d"));
        $interval_end = strtotime('-1 days', $today);
        $interval_start = strtotime(date("Y/m/d", strtotime("-3 month", $interval_end)));

        $stripe = new \Stripe\StripeClient(
            $access_token
        );
        $stripe->reporting->reportRuns->create([
            'report_type' => 'balance.summary.1',
            'parameters' => [
                'interval_start' => $interval_start,
                'interval_end' => $interval_end,
            ],
        ]);
    }

    public function merchantFeeReportRun(Request $request)
    {

        $user = Auth::user();
        $stripeAccounts = $user->getStripeAccountConnectIds();
        $stripe_fee = 0;
        $report_init = false;

        if ($stripeAccounts->count()) {
            foreach ($stripeAccounts as $account) {
                $stripe_record_exist = StripeReport::where('user_id', Auth::user()->id)->where('stripe_user_id', $account->stripe_user_id)->where('start_date', $request->s_date)->where('end_date', $request->e_date)->where('report_status', true)->first();

                if ($stripe_record_exist) {
                    $stripe_fee += $stripe_record_exist->stripe_fee;
                } else {
                    $stripe = new \Stripe\StripeClient(
                        $account->access_token
                    );

                    $start_date = new DateTime($request->s_date, new DateTimeZone($account->time_zone));
                    $end_date = new DateTime($request->e_date, new DateTimeZone($account->time_zone));
                    // $end_data_utc = new DateTime($request->e_date . ' 00:00:00 UTC');

                    $report_status = $stripe->reporting->reportRuns->create([
                        'report_type' => 'balance.summary.1',
                        'parameters' => [
                            // 'interval_start' => strtotime($request->s_date),
                            // 'interval_end' => strtotime($request->e_date),
                            'interval_start' => $start_date->format('U'),
                            'interval_end' => $end_date->format('U'),
                            "timezone" => $account->time_zone
                        ],
                    ]);



                    $stripe_report_record = array(
                        'user_id' => Auth::user()->id,
                        'object_id' => $report_status->id,
                        'stripe_user_id' => $account->stripe_user_id,
                        'start_date' => $request->s_date,
                        'end_date' => $request->e_date,
                        'report_type' => $report_status->report_type,
                        'report_status' => false
                    );

                    StripeReport::updateOrCreate(['stripe_user_id' => $account->stripe_user_id, 'start_date' => $request->s_date, 'end_date' => $request->e_date], $stripe_report_record);
                    $report_init = true;
                }
            }
        }
        if ($report_init) {
            return null;
        } else {
            return  $stripe_fee;
        }
    }

    public function reportWebhookHandler(Request $request)
    {
        $account_id = $request->account;
        $data = $request->data;
        $status = $data['object']['status'];
        $url = $data['object']['result']['url'];

        $report_type = $data['object']['report_type'];
        $object_id = $data['object']['id'];


        $stripe_account = StripeAccount::where('stripe_user_id', $account_id)->first();
        $access_token = $stripe_account->access_token;

        try {
            return response()->json([
                'success',
            ], 200);
        } finally {
            if ($report_type == 'balance.summary.1') {

                $this->stripeFeeReportHandler(
                    $url,
                    $access_token,
                    $stripe_account->user_id,
                    $stripe_account->stripe_user_id,
                    $object_id
                );
            } else {
                ProcessStripeCsvReport::dispatch(
                    $url,
                    $access_token,
                    $stripe_account->user_id,
                    $stripe_account->stripe_user_id
                );
            }
        }
    }


    public function stripeFeeReportHandler($report_url, $access_token, $user_id, $stripe_user_id, $object_id)
    {

        $options = array('http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $access_token,
        ));
        $context = stream_context_create($options);
        $response = file_get_contents($report_url, false, $context);

        $fp = fopen("php://temp", 'r+');
        fputs($fp, $response);
        rewind($fp);
        $csv_data = [];
        while (($data = fgetcsv($fp)) !== false) {
            $csv_data[] = $data;
        }
        foreach ($csv_data as $key => $values) {
            if ($key == 3) {
                $transaction = array(
                    'stripe_fee' => $values[2],
                    'report_status' => true,
                    'report_url' => $report_url
                );
                StripeReport::updateOrCreate(['user_id' => $user_id, 'stripe_user_id' => $stripe_user_id, 'object_id' =>  $object_id], $transaction);
            } else {
                continue;
            }
        }
    }
    public function chargeWebookHandler(Request $request)
    {
        $stripe_user_id = $request->account;
        $stripe_account = StripeAccount::where('stripe_user_id', $stripe_user_id)->first();
        if ($stripe_account) {
            $balance_transaction_id = $request->data['object']['balance_transaction'];
            $balance_transaction = $this->getBalanceTransaction($balance_transaction_id, $stripe_account->access_token);

            $data = array(
                'user_id' => $stripe_account->user_id,
                'stripe_user_id' => $stripe_account->stripe_user_id,
                'balance_transaction_id' => $balance_transaction->id,
                'created' => "'" . date("Y/m/d  H:i:s", $balance_transaction->created) . "'",
                'available_on' => "'" . date("Y/m/d  H:i:s", $balance_transaction->available_on) . "'",
                'currency' => $balance_transaction->currency,
                'gross' => $balance_transaction->amount,
                'fee' => $balance_transaction->fee,
                'net' => $balance_transaction->net,
                'reporting_category' => $balance_transaction->reporting_category,
                'description' => $balance_transaction->description,
            );
            StripeBalanceTransactionsReport::updateOrCreate(['user_id' => $stripe_account->user_id, 'stripe_user_id' => $stripe_account->stripe_user_id, 'balance_transaction_id' => $balance_transaction->id], $data);
        }

        return response()->json([
            'success',
        ], 200);
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
            return array('name' =>  $response['business_profile']['name'], 'time_zone' => $response['settings']['dashboard']['timezone']);
        } else {
            return null;
        }
    }

    public function getReportContent($report_url, $access_token, $user_id, $stripe_user_id)
    {

        $options = array('http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $access_token,
        ));
        $context = stream_context_create($options);
        $response = file_get_contents($report_url, false, $context);

        $fp = fopen("php://temp", 'r+');
        fputs($fp, $response);
        rewind($fp);
        $csv_data = [];
        while (($data = fgetcsv($fp)) !== false) {
            $csv_data[] = $data;
        }
        foreach ($csv_data as $key => $values) {
            if ($key == 0) {
                continue;
            } else {
                $transaction = array(
                    'user_id' => $user_id,
                    'stripe_user_id' => $stripe_user_id,
                    'balance_transaction_id' => $values[0],
                    'created' => $values[1],
                    'available_on' => $values[2],
                    'currency' => $values[3],
                    'gross' => $values[4],
                    'fee' => $values[5],
                    'net' => $values[6],
                    'reporting_category' => $values[7],
                    'description' => $values[8],
                );
                //Save the transacion in the table
                StripeBalanceTransactionsReport::updateOrCreate(['user_id' => $user_id, 'stripe_user_id' => $stripe_user_id, 'balance_transaction_id' => $transaction['balance_transaction_id']], $transaction);
            }
        }
    }

    public function getBalanceTransaction($transaction_id, $access_token)
    {
        $stripe = new \Stripe\StripeClient(
            $access_token
        );
        $balance_obj = $stripe->balanceTransactions->retrieve(
            $transaction_id,
            []
        );
        // $data = json_decode($balance_obj, true);
        return $balance_obj;
    }

    public function getStripeChargebacks(Request $request)
    {

        // return Cache::tags(['SHOPIFY:' . Auth::user()->id])->remember('STORES', env('REDIS_TTL'), function () {
        return Cache::tags(['STRIPE' . Auth::user()->id])->remember('STRIPE_CHARGEBACK_' . $request->s_date . '_' . $request->e_date, env('REDIS_TTL'), function () use ($request) {

            $user = Auth::user();
            $stripeAccounts = $user->getStripeAccountConnectIds();
            $stripe_chargebacks = [];

            if ($stripeAccounts->count()) {
                foreach ($stripeAccounts as $account) {
                    if ($account->enabled_on_dashboard) {
                        $stripe = new \Stripe\StripeClient(
                            $account->access_token
                        );
                        $disputes = $stripe->disputes->all([
                            'limit' => 100,
                            'created' => array(
                                'gte' => strtotime($request->s_date),
                                'lte' => strtotime($request->e_date),
                            ),
                        ]);
                        foreach ($disputes->autoPagingIterator() as $dispute) {
                            array_push($stripe_chargebacks, array('created' => $dispute->created, 'amount' => $dispute->amount, 'status' => $dispute->status, 'currency' => $dispute->currency));
                        }
                    }
                }

                return $stripe_chargebacks;
            } else {
                return 0;
            }
        });
    }
}
