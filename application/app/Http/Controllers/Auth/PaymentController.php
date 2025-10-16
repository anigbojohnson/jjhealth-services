<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Solutions;
use App\Models\Payment;
use App\Models\MedicalCertificate;


use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //


public function make($request) 
{
    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    // Create a PaymentIntent with manual capture method
    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => $request->cost * 100,  // Stripe expects amount in cents
        'currency' => 'aud',
        'payment_method_types' => ['card'],
        'capture_method' => 'manual',  // Authorize only, capture later
        'description' => $request->solution_name,
    ]);

    if (isset($paymentIntent->id) && $paymentIntent->id != "") {
        session()->put('payment_intent_id', $paymentIntent->id);

        // Return client secret to the frontend to confirm the payment
        return $paymentIntent->client_secret;
       
       
    } else {
        return redirect()->route('cancel');
    }
}

   
    
}
