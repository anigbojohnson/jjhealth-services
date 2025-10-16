<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\custom\service\EmailVerificationService;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ForgottenPasswordController extends Controller
{
    protected $forgottenPasswordService;

    public function __construct(EmailVerificationService $forgottenPasswordService)
    {
        $this->forgottenPasswordService = $forgottenPasswordService;

    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            // Return validation errors as JSON response
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $checkUser =  EmailVerification::where('email', $request->email)
        ->first();
    
        if( $checkUser ){
            $checkUser->delete();
        }
        $user = User::where('email', $request->email)
            ->first();

        if( $user ){
            $userEmail = new EmailVerification();
            $userEmail->first_name = "";
            $userEmail ->last_name = "";
            $userEmail ->email = $request->email;
            $userEmail ->password = "";
            $userEmail ->save();

            $this->forgottenPasswordService->sendVerificationLink($user,"forgotten-password");
            return response()->json(['message' => 'messege sent to your email.']);

        }
        return response()->json(['error' => 'failure to send to your email'], 422);

    }

    public function changePassword($email , $token)
    {
        $user = EmailVerification::where('email', $email)
        ->where('token', $token)  // Include the token if needed
        ->where('expires_at', '>', Carbon::now())  // Ensure expire_at is less than current time
        ->first();

        // If the user doesn't exist or the token is invalid, return an error
        if (!$user) {
            $message = "Invalid credentials";
    
            return view('auth.change-password', compact('email', 'token', 'message'));
        }else{
            $user->update([
                'change_time'=>Carbon::now()->addMinutes(10),
                'expires_at' => Carbon::now(),
            ]);
            $message = "You have ten minutes to update your password.";
    
            return view('auth.change-password', compact('email', 'token', 'message'));
        }
       
    }


    public function saveChangedPassword(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ], [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.'
        ]);

        // Retrieve the user by email and verify the token
              $token = $request->input('token');
             $email = $request->input('email');
            $user = EmailVerification::where('email', $email)
            ->where('token', $token)  // Include the token if needed
            ->first();

        // If the user doesn't exist or the token is invalid, return an error
        if (!$user) {
       
            return response()->json([
                'status' => 'error',
                'message' => 'Your token is invalid'
            ], 400);
        }
        $user = EmailVerification::where('email', $email)
        ->where('change_time', '>', Carbon::now())  // Ensure expire_at is less than current time
        ->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your token has expired, 10 minutes allotted has elapsed'
            ], 400);    
        }
        $user->delete();
        $user = User::where('email', $email)->first();
        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        // Redirect the user with a success message
        return response()->json([
            'status' => 'success',
             "message"=>'Password changed successfully, you can now login'

        ],200);
        
    }
}
