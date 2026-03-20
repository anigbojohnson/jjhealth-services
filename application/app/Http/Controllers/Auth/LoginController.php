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
    public function login(Request $request,$param, $action)
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
            // Authentication successful, redirect to the intended page
       if ($param ==="login_form") {
             return redirect()->route('/');

        } else {
            $action = session()->get('action');
            
            $data = ['param' => session()->get('param'),'user' =>  Auth::user() ]; // Data to pass
            return view('auth.'.$action, $data);

        }

        } else {
            // Authentication failed, redirect back with error message
            return redirect()->back()->withInput($request->only('email'))->withErrors([
                'error' => 'These credentials do not match our records.',
            ]);
        }
    }

}
