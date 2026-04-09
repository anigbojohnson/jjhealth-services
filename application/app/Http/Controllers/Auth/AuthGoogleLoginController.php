<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailVerification;
use App\Models\Solutions;
use App\Models\Category;
use App\Services\TracingService;

class AuthGoogleLoginController extends Controller
{
    //
public function __construct(private TracingService $tracing) {}

public function redirect()
{
        $query = http_build_query([
            'client_id'     => config('services.google.client_id'),
            'redirect_uri'  => config('services.google.redirect_login'),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'offline',
            'prompt'        => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
}
public function callback(Request $request) {
      

        // Check for errors from Google
        if ($request->has('error')) {
            return redirect('/login')->withErrors(['error' => 'Google authentication was cancelled.']);
        }


        // Step 1: Exchange code for access token
        $tokenResponse = Http::post('https://oauth2.googleapis.com/token', [
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => config('services.google.redirect_login'),
            'grant_type'    => 'authorization_code',
            'code'          => $request->query('code'),
        ]);

        if ($tokenResponse->failed()) {
            return redirect('/login')->withErrors(['error' => 'Failed to retrieve access token from Google.']);
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // Step 2: Get user info from Google
        $userResponse = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($userResponse->failed()) {
            return redirect('/login')->withErrors(['error' => 'Failed to retrieve user info from Google.']);
        }

        $googleUser = $userResponse->json();
        // $googleUser contains: sub, email, name, given_name, family_name, picture

        $userExists = User::where('email', $googleUser['email'])
        ->where('provider', '!=', 'google')
        ->exists();

        if($userExists){ 
            // Redirect with error message and additional data
            return redirect()->route('login')->withErrors(['error' => 'You have different means of login.']);
        }
        $user = User::where([
            'provider-id' => $googleUser['sub'],
            'provider' =>'google'
        ])->first();

            if(!$user){

                $user = User::create([
                    'provider-id' => $googleUser['sub'],
                    'provider' =>'google',                
                    'first_name' => $googleUser['given_name'],
                    'last_name'=>$googleUser['family_name'],
                    'email' => $googleUser['email'],
                    'email_verified_at'=> now()]
                );

              
            }
         Auth::login($user);
         $solutionId = data_get(session('credentials'), 'solution_id');
        if ($solutionId !== null) {


            if (str_starts_with($solutionId, 'MC')) {
                if ($solutionId ==='MC01') {
                    return view('medical-certificate.work-medical-certificate');
                } elseif ($solutionId === 'MC02') {
                    return view('medical-certificate.studies-medical-certificate');
                } elseif ($solutionId === 'MC03') {
                    return view('medical-certificate.carers-Leave-certificate');
                } elseif ($solutionId === 'MC04') {
                    return view('medical-certificate.travel-and-holiday-certificate');
                }
            } elseif (str_starts_with($solutionId, 'TR')) {
                return view('treatment.telehealth-request');
            } elseif (str_starts_with($solutionId, 'R')) {
                return view('referals.specialist-referrals-request');
            }

        } else {
             return redirect()->route('dashboard');

        }

   
    }
}
