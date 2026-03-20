<?php

namespace App\Http\Controllers\Referrals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\SpecialistReferrals;
use Illuminate\Support\Facades\Auth;
use App\Models\Solutions;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;



class SpecialistReferralsController extends Controller
{
    

    public function personalDetails(Request $request)
    {
        $validatedData = $request->validate([
            
            'fname' => 'required|string',
            'lname' => 'required|string',
            'pnumber' => [
                'required',
                'regex:/^(?:\+61|0)[2-478](?:[ -]?[0-9]){8}$/'
            ],
            'dob' => 'required|date|before:-18 years',
            'gender' => 'required|in:male,female,not say',
            'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
            'address' => 'required|string'
    ]);

    session()->put('personalDetails', $validatedData);

    return response()->json(['message' => 'success'], 200);

    }

    public function consultationDetails(Request $request)
    {
        $validatedData = $request->validate([
            'requestReason' => 'required|string|max:255',
            'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
            'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'
        ]);

        return response()->json(['message' => ''], 200);

    }

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'SR01')->latest('id')->first();
        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
   

    public function  saveConsultDetails(Request $request)
    {
        $validatedData = $request->validate([
            'requestReason' => 'required|string|max:255',
            'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
            'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'
        ]);

        $fileName ="";
        if ($request->hasFile('fileUpload')) {
            // Get the file content
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = base64_encode(file_get_contents($file));
            $filePath = Storage::disk('s3')->putFileAs('user-temp-file/'. Auth::user()->email, $file, $fileName, 'public');

        }

        $userData = session()->get('personalDetails');


        $user = User::updateOrCreate(
            ['email' => Auth::user()->email], // Condition to find the user
            [
                'first_name' => $userData['fname'],
                'last_name' => $userData['lname'],
                'phone_number' => $userData['pnumber'],
                'dob' => $userData['dob'],
                'gender' => $userData['gender'],
                'indigene' =>$userData['indigene'],
                'address' => $userData['address'],
            ]
        );

        
    $sr = SpecialistReferrals::create([
            'user_email' => auth()->user()->email,  // Assuming the user is authenticated
            'request_reason' => $validatedData['requestReason'],
            'image_uploaded' => $validatedData['medicalConditionImage'], // The file path in the storage
            'file_name' => $validatedData['medicalConditionImage']=='Yes' ? $fileName :null, // The original file name
            'request_status'=>"new request"
        ]);

        $solutions = Solutions::where('solution_id', 'SR01')->latest('id')->first();

        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id =  session('credentials')->id;
        $payment->customer_email = Auth::user()->email;
        $payment->specialist_referrals_id = $sr->id;    
        $payment->payment_status = "pending";    

        $payment->save();


        session()->forget(['payment_intent_id']);

        return response()->json([
            'redirect_url' => route('specialist-referral-home', ['messege' => "Your payment is pending when your request is fullfilled"])
        ]);

    }
}
