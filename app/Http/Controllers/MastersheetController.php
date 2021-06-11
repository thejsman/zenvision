<?php

namespace App\Http\Controllers;

use App\DailyNetequity;
use App\User;
use Auth;
use Carbon\Carbon;

class MastersheetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('mastersheet', [
            'component' => 'mastersheet',
        ]);
    }

    public function show($group, $component)
    {
        return view('mastersheet', compact('group', 'component'));
    }

    public function getNetEquityData()
    {
        $user = Auth::User();
        $netEquityArray = DailyNetequity::where('user_id', $user->id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->select('net_equity', 'created_at')->get();
        if (count($netEquityArray)) {
            return $netEquityArray;
        } else {
            return array(0, 0, 0, 0, 0, 0, 0);
        }

    }
}
