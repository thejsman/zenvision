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
        $data['type'] = $request->type;
        $data['amount'] = $request->amount;

        SupplierPayable::create($data);
    }
    public function edit(Request $request)
    {
        if ($request->has('id')) {
            $supplier_payable = SupplierPayable::find($request->id);
            $supplier_payable->title = $request->title;
            $supplier_payable->amount = $request->amount;
            $supplier_payable->save();
        }
    }
    public function destroy($id)
    {
        SupplierPayable::destroy($id);
    }
}
