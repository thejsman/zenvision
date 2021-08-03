<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;

class ShopifyRegisterController extends Controller
{
    public function index()
    {
        return view('shopify-register', [
            'component' => 'shopify-register'
        ]);
    }
    public function store(Request $request)
    {
        // 'shopify_password' => Hash::make('the-password-of-choice'),
        // 'shopify_email' => 'the-email@example.com',
        // 'shopify_firstname' => 'F name',
        // 'shopify_lastname' => 'L name',
        // 'shopify_phone' => '1234567980',

        // $request->session()->get('google_access_token')

        // $user = new User();
        // $user->password = Hash::make($request->password);
        // $user->email = $request->session()->get('shopify_email');
        // $user->firstname = $request->session()->get('shopify_firstname');
        // $user->lastname = $request->session()->get('shopify_lastname');
        // $user->phone = $request->session()->get('shopify_phone');
        // $user->save();
        // dd('register a user here');

    }
}
