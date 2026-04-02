<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class LoginController extends Controller
{
        /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function loginForm(Request $request)
    {
        return view('auth.login');
    }
      /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        
        // Validate the form data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $userExists = User::where('email', $request->email)
        ->where('provider', '!=', 'form register')
        ->exists();
    
        if($userExists){
            return redirect()->route('login', ['param' => session()->get('param'), 'action' => session()->get('action')])
                   ->with('error', 'You have different means of login.');

        }

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

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
            } elseif (str_starts_with($solutionId, 'PR')) {
                return view('pathology.pathology-request');
            }

        } else {
             return redirect()->route('dashboard');

        }

        } else {
            // Authentication failed, redirect back with error message
            return redirect()->back()->withInput($request->only('email'))->withErrors([
                'error' => 'These credentials do not match our records.',
            ]);
        }
    }

}
