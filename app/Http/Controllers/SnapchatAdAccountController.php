<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SnapchatAdAccount;
use Auth;

class SnapchatAdAccountController extends Controller
{
    public function store(Request $request)
    {
        $ad_account = array(
            'user_id' => Auth::user()->id,
            'ad_account_id'  => $request->id,
            'ad_account_name'  => $request->name,
            'organization_id' => $request->organization_id,
            'type' => $request->type,
            'currency' => $request->currency,
            'status' => $request->status,
            'timezone' => $request->timezone
        );
        SnapchatAdAccount::create($ad_account);
    }
}
