<?php

namespace App\Jobs;

use App\StripeAccount;
use App\StripeBalanceTransactionsReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStripeCsvReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $report_url;
    public $access_token;
    public $user_id;
    public $stripe_user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($report_url, $access_token, $user_id, $stripe_user_id)
    {
        $this->report_url = $report_url;
        $this->access_token = $access_token;
        $this->user_id = $user_id;
        $this->stripe_user_id = $stripe_user_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $report_url = 'https://files.stripe.com/v1/files/file_1IeNN5LInuel29pDXtOOe8LY/contents';
        // $access_token = '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';

        $options = array('http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $this->access_token,
        ));
        $context = stream_context_create($options);
        $response = file_get_contents($this->report_url, false, $context);

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
                    'user_id' => $this->user_id,
                    'stripe_user_id' => $this->stripe_user_id,
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
                StripeBalanceTransactionsReport::updateOrCreate(['user_id' => $this->user_id, 'stripe_user_id' => $this->stripe_user_id, 'balance_transaction_id' => $transaction['balance_transaction_id']], $transaction);
            }
        }
        StripeAccount::where(['user_id' => $this->user_id, 'stripe_user_id' => $this->stripe_user_id])->update('report_status', true);
    }
}
