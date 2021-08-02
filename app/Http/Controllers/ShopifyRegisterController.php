<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopifyRegisterController extends Controller
{
    public function index()
    {
        return view('shopify-register', [
            'component' => 'shopify-register'
        ]);
    }
}
