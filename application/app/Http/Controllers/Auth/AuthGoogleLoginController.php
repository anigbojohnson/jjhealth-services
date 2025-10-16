<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Auth;

class AuthGoogleLoginController extends Controller
{
    //

    
public function redirect(Request $request)
{
    // Retrieve query parameters
    session()->put('page', $request->query('page'));
    return Socialite::driver('google')->redirect();
    }

public function callback() {
       
        $googleUser = Socialite::driver('google')->user();

        $userExists = User::where('email', $googleUser->email)
        ->where('provider', '!=', 'google')
        ->exists();

        if($userExists){
            $page = session()->get('page');
            session()->forget('page');
            $action = session()->get('action');
            $param = session()->get('param');
    
            // Redirect with error message and additional data
            return redirect()->route($page, [
                'param' => $param,
                'action' => $action
            ])->withErrors(['error' => 'You have different means of login.']);

        }
        $user = User::where([
            'provider-id' => $googleUser->id,
            'provider' =>'google'
        ])->first();

            if(!$user){

                $user = User::create([
                    'provider-id' => $googleUser->id,
                    'provider' =>'google',                
                    'first_name' => $googleUser->user['given_name'],
                    'last_name'=>$googleUser->user['family_name'],
                    'email' => $googleUser->email,
                    'email_verified_at'=> now()]
                );

              
            }


            Auth::login($user);

            if (session()->has('action')  && session()->get('action') != '_') {
                $action = session()->get('action');

                $data = ['param' => session()->get('param'),'user' => $user]; // Data to pass

                return view('auth.'.$action, $data);
            } else {
                return redirect('/');
            }
   

 
    }
}
