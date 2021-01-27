<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\BankAccount;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $bankAccount = [];
        $bankAccount['user_id'] = Auth::user()->id;
        $bankAccount['access_token']  = $request->access_token;
        $bankAccount['bank_user']  = $request->user;
        $bankAccount['bank_name']  = $request->institution_name;



        BankAccount::updateOrCreate(['user_id' => Auth::user()->id], $bankAccount);
    }
}
