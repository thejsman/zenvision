<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\DailyNetequity;
use App\Http\Controllers\DashboardController;

use Illuminate\Filesystem\Filesystem;

class dailyNetEquityJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netEquity:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate daily net Equity of the user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            $dailyNetEquity = new DailyNetEquity;
            $dailyNetEquity->user_id = $user->id;
            $data = DashboardController::mastersheet($user);

            $dailyNetEquity->net_equity =
                $data['total_cash'] +
                $data['total_inventory'] +
                $data['total_reserves'] -
                $data['total_credit_card'] -
                $data['total_supplier_payable'];
            $dailyNetEquity->save();
        }
    }
}
