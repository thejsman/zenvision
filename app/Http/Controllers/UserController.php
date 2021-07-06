<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserController extends Controller
{
    public function getStores(User $user) {
        return $user->stores;
    }
    public function index() {
        $user = Auth::user();
        return $user;
    }
    public function edit(Request $request) {

        $user = Auth::user();
        if($user->id == $request->id) {
            $user->firstname = $request->firstName;
            $user->lastname = $request->lastName;
            $user->email = $request->email;
            $user->phone = $request->phoneNumber;
            $user->save();
            return $user;
        } else {
            return [];
        }
    }
    public function changePassword(Request $request) {
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

    }

}
