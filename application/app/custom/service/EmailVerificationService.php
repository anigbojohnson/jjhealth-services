<?php

namespace App\custom\service;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationNotification;
use Carbon\Carbon;

 
use App\Models\EmailVerification;


class EmailVerificationService{


    public function sendVerificationLink(object $user,string $notificationType):void
    {
        Notification::send($user , new EmailVerificationNotification($this->generateToken($user->email,$notificationType)));
    }
    public function generateToken(string $email, string $notificationType): string
    {
  
        // Generate a random string for the token
        $token = bin2hex(random_bytes(32)); // Generate a 64-character hexadecimal token

        // Calculate the expiration time (10 minutes from now)
        $expiresAt = Carbon::now()->addMinutes(10);
        // Store the token and expiration time in the database

        $savedVerification = EmailVerification::where('email', $email)->update([
            'token' => $token,
            'expires_at' => $expiresAt,
            'solution_id' => session('credentials')->solution_id
        ]);
  

        if($savedVerification){
            $appUrl = Config::get('app.url');
            // Return the generated token
            $verificationLink = "";
            if($notificationType ==="forgotten-password"){
                $verificationLink = $appUrl . '/change-password/' . $email . '/' . $token;
            }
            if($notificationType ==="verify-email"){
                $verificationLink = $appUrl . '/verify-email/' . $email . '/' . $token;

            }

        
            return $verificationLink;
        }
        return $verificationLink;

    }
}