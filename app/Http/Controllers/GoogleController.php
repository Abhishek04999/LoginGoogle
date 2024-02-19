<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class GoogleController extends Controller
{
    //
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
        try {
            $user = Socialite::driver('google') ->stateless()-> user();
            $finduser= User::where('email',$user->getEmail())->first();
            if(!$finduser)
            {
              $saveUser = User::updateOrCreate([
                'google_id'=> $user->getId(),
               ],
               [
                'name' => $user->getName(),
                'email'=> $user->getEmail(),
                'password' => Hash::make($user->getName().'@'.$user->getId())
               ]
            );
            }
            else {
                $saveUser =  User::where('email', $user->getEmail())->update(['google_id' => $user->getId(),
            ]);
            $saveUser = User::where('email',  $user->getEmail())->first();
            }
            Auth::loginUsingId( $saveUser->id);
            return redirect('/home');

        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
