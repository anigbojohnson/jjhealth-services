<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use App\Models\User;

use Illuminate\Http\Request;

class AuthMicrosoftLoginController extends Controller
{
    //

public function redirect(Request $request)
{
    // Retrieve query parameters
    session()->put('page', $request->query('page'));
    $query = http_build_query([
        'client_id' => config('services.microsoft.client_id'),
        'redirect_uri' => route('auth.microsoft.callback'),
        'response_type' => 'code',
        'scope' => 'User.Read', // Adjust scopes as needed
        'state' => csrf_token() 
    ]);

    return redirect('https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . $query);
    }

public function callback(Request $request) {
    
    $httpClient = new Client();
    $response = $httpClient->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
        'form_params' => [
            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'code' => $request->input('code'),
            'redirect_uri' => route('auth.microsoft.callback'),
            'grant_type' => 'authorization_code',
        ],
    ]);

    $accessToken = json_decode((string) $response->getBody(), true)['access_token'];
    $userEndpoint = 'https://graph.microsoft.com/v1.0/me';

    $response = $httpClient->request('GET', $userEndpoint, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ],
    ]);

    // Decode the JSON response body to an associative array
    $userData = json_decode($response->getBody(), true);
    $userExists = User::where('email', $userData['mail'])
                  ->where('provider', '!=', 'microsoft')
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
            'provider-id' =>  $userData['id'],
            'provider' =>'microsoft'
        ])->first();
            if(!$user){
                $user = User::create([
                    'provider-id' =>  $userData['id'] ,
                    'provider' =>'microsoft',                
                    'first_name' =>  $userData['givenName'],
                    'last_name'=> $userData['surname'],
                    'email' => $userData['mail'],
                    'email_verified_at'=> now()]
                );

            }

            Auth::login($user);


            if (session()->has('action')&& session()->get('action') != '_') {
                $action = session()->get('action');
                session()->forget('action');

                $data = ['param' => session()->get('param'),'user' => $user]; // Data to pass
                session()->forget('param');

                return view('auth.'.$action, $data);
            } else {
                return redirect('/');

            }

    // Output the user data
    }
}
