<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubscriptionCost;
use App\User;
use Auth;

class SubscriptionCostController extends Controller
{
    public function index()
    {
        return SubscriptionCost::where('user_id', Auth::user()->id)->where('isDeleted', false)->get();
    }

    public function store(Request $request)
    {
        $data = [];
        $data['user_id'] = Auth::user()->id;
        $data['subscription_name'] = $request->subscription_name;
        $data['subscription_price'] = $request->subscription_price;
        $data['billing_period'] = $request->billing_period;
        $data['starting_date'] = $request->starting_date;

        SubscriptionCost::create($data);
    }

    public function destroy($id)
    {

        $result = SubscriptionCost::where('id', $id)->delete();
        return $result;
    }

    public function update($id, Request $request)
    {
        $cost = SubscriptionCost::find($id);

        $cost['user_id'] = Auth::user()->id;
        $cost['subscription_name'] = $request->subscription_name;
        $cost['subscription_price'] = $request->subscription_price;
        $cost['billing_period'] = $request->billing_period;
        $cost['starting_date'] = $request->starting_date;

        $cost->save();
    }

    public function endSubscription($id)
    {
        $cost = SubscriptionCost::find($id);
        $cost['end_date'] = now();
        $cost->save();
    }
}
