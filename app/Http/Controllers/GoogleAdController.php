<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GoogleAd;
use App\Http\CustomRequests;

class GoogleAdController extends Controller
{
    public function index(Request $request)
    {
    }
    public function toogleAdAccount(Request $request)
    {
        $account = GoogleAd::find($request->id);
        $account->enabled_on_dashboard = !$request->enabled_on_dashboard;
        $account->save();
    }

    public function destroy(Request $request)
    {
        $account = GoogleAd::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
