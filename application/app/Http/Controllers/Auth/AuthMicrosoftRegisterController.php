<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Http\Request;

class AuthMicrosoftRegisterController extends Controller
{

public function redirect(Request $request)
{
    // Retrieve query parameters
    $query = http_build_query([
        'client_id' => config('services.microsoft.client_id'),
        'redirect_uri' => config('services.microsoft.redirect_register'),
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
            'redirect_uri' => config('services.microsoft.redirect_register'),
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
            // Redirect with error message and additional data
              return redirect()->route('register')->withErrors(['error' => 'You have different means of login.']);

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
