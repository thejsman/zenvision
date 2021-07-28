<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\SupplierPayable;

class SupplierPayableController extends Controller
{
    public function get()
    {
        $data = SupplierPayable::where('user_id', Auth::user()->id)->get();
        return $data;
    }
    public function store(Request $request)
    {
        $data = [];
        $data['user_id'] = Auth::user()->id;
        $data['title'] = $request->title;
        $data['amount'] = $request->amount;

        SupplierPayable::create($data);
    }
}
