<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\custom\service\EmailVerificationService;
use App\Models\EmailVerification;



class RegisterController extends Controller
{

    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;

    }
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request,$param, $action){
    return view('auth.register', compact('param', 'action'));
}

    

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function register(Request $request,  $param,$action)
    {
   
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', // At least 8 characters
                'regex:/[a-z]/', // At least one lowercase letter
                'regex:/[A-Z]/', // At least one uppercase letter
                'regex:/[0-9]/', // At least one number
                'regex:/[@$!%*?&#]/' // At least one special character
            ],
            'password_confirmation' => 'required|string|min:8|same:password',
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'The password must be at least 8 characters.',
            'password_confirmation.same' => 'The password confirmation does not match.',
        ],[
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'password_confirmation'=>'password confirmation'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        
        $checkUser =  EmailVerification::where('email', $request->email)
        ->first();
    
        if( $checkUser ){
            $checkUser->delete();
        }

        $userExists = User::where('email', $request->email)
        ->where('provider', '!=', 'form register')
        ->exists();
        
        if($userExists){
            return redirect()->route('login')->with('error', 'You have different means of login.');
        }
        // Create a new user instance
        $user = new EmailVerification();
        $user->first_name = $request->fname;
        $user->last_name = $request->lname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        if($user->save()){
             
            $this->emailVerificationService->sendVerificationLink($user, "verify-email");

            return redirect()->route('register', [
                'param' => $param,
                'action' => $action
            ])->with('success', 'Please check your email to verify and complete registration .');

        }
        return redirect()->route('showRegistrationForm')->with('error', 'Registration failed, please try again.');

    }
}
