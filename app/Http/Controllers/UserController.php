<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Auth;

class UserController extends Controller
{
    public function index() {
        return view('profile', [
            'component' => 'profile'
        ]);
    }
    public function getStores(User $user) {
        return $user->stores;
    }
    public function getUser() {
        $user = Auth::user();
        return $user;
    }
    public function edit(Request $request) {
        $user = Auth::user();
      $validator =   $this->validate($request, [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email,'.$user->id],
            'phoneNumber' => ['required', 'string', 'max:255'],
        ]);
      
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
        $this->validate($request, [
            'password' => ['required', 'string', 'min:6',]
        ]);
        $user->password = Hash::make($request->password);
        $user->save();

    }

}
