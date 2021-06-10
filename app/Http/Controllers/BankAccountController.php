<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Auth;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {

        $bankAccount = [];
        $bankAccount['user_id'] = Auth::user()->id;
        $bankAccount['access_token'] = $request->access_token;
        $bankAccount['bank_user'] = $request->user;
        $bankAccount['bank_name'] = $request->institution_name;
        $bankAccount['isDeleted'] = false;
        BankAccount::updateOrCreate(['user_id' => Auth::user()->id, 'bank_user' => $request->user, 'bank_name' => $request->institution_name], $bankAccount);
    }
    public function getBankAccounts()
    {
        return Auth::user()->getBankAccounts();
    }
    public function destroy(Request $request)
    {
        $account = BankAccount::find($request->id);
        $account->isDeleted = true;
        $account->save();
    }
}
