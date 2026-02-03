<?php
namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;


class VerifyEmailController extends Controller
{


    public function send($email, $token)
    {
     $user_verified= EmailVerification::where('email', $email)
            ->first();

           

        if (!$user_verified ||   $token !== $user_verified->token ||    Carbon::parse($user_verified->expires_at) < Carbon::now() ){
            // Handle invalid email or token
            return redirect()->route('register',['param' => 'error', 'action' => 'error'])->with('error',  'invalid or expired token, please register again');
        }

        $user = User::create([
            'first_name'=>$user_verified->first_name,
            'last_name'=>$user_verified->last_name,
            'password'=>$user_verified->password,
            'email' => $user_verified->email,
            'email_verified_at'=>Carbon::now(),
            'provider'=> 'form register'
        ]);

     
        Auth::login($user);
        $user_verified->delete();
        return redirect()->route('/')->with('success',  'successfull registration , you are now login.');

    }
}
