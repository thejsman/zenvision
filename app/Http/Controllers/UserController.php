<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    //
    public function getStores(User $user) {
        // return User::find(auth()->user()->id)->stores;
        return $user->stores;
    }
}
