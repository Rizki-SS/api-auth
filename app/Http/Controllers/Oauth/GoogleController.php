<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $gUser = Socialite::driver('google')->stateless()->user();
            $user = User::Where('email', $gUser->getEmail())
                    ->first();
       
            if(!$user){
                $user = User::create([
                    'name' => $gUser->name,
                    'email' => $gUser->email,
                    'password' => encrypt(Str::random(10))
                ]);
            }

            Auth::login($user);
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Failed at try to login, msg : " . $e->getMessage() ], 400);
        }
    }
}
