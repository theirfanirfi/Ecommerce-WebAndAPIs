<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\MyClasses\CommonResponse as CR;
use Illuminate\Support\Facades\Hash;
class UserControllerAPI extends Controller
{
    //
    public function login(Request $req){
        $email = $req->input('email');
        $password = $req->input('password');

        if($email == "" || $password == ""){
           return CR::returnEmpty();
        }else {
            $user = User::where(['email' => $email,'password' => $password]);
            if($user->count() > 0){
                $user = $user->first();
                $user->token = Hash::make($user->email.$user->created_at.time());
                $user->save();
            return response()->json([
                'user' => $user->first(),
                'isLoggedIn' => true,
                'isError' => false,
                'isAuthenticated' => true,
            ]);
            }else {
                return CR::invalidCreds();
            }
        }
    }
}
