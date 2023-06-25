<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success["user"] = $user;
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 

            return response()->json(['success' => $success], 200); 
        } 
        return response()->json(['error'=>'Unauthenticated'], 401); 
    }

    public function isAuth() {
        $user = Auth::user();
        $user->roles;
        return response()->json($user);
    }

    public function revoke() {
        $user = Auth::user();
        if ($user->token()->revoke()) {
            return response()->json(['success' => "logout"], 200); 
        };

        return response()->json(['error'=>'Unauthenticated'], 401); 
    }
}
