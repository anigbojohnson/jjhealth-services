<?php

namespace App\Http\Controllers\Telehealth;
use Illuminate\Support\Facades\Validator;
use App\Models\Treatment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

use App\Models\User;
use App\Models\Solutions;
use App\Http\Controllers\Payment as PaymentController;
use Illuminate\Support\Facades\Auth;

class TelehealthController extends Controller
{
    //
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
        // Define validation rules
        $rules = [
            'preExistingHealth' => 'required|string',
            'informationPreExistingHealthYes' => 'required_if:preExistingHealth,Yes|nullable|string',
            'medicationsRegularly' => 'required|string',
            'medicationsRegularlyInfo' => 'required_if:medicationsRegularly,Yes|nullable|string',
            'startDateSymptoms' => 'required|date',
            'detailedSymptoms' => 'required|string',
            'treatment_category' => 'required|string'
        ];

        // Define custom error messages
        $messages = [
            'preExistingHealth.required' => 'Pre-existing health condition is required.',
            'informationPreExistingHealthYes.required_if' => 'Information about pre-existing health is required if pre-existing health is "yes".',
            'medicationsRegularly.required' => 'Information about regular medications is required.',
            'medicationsRegularlyInfo.required_if' => 'Details about regular medications are required if medications are taken regularly.',
            'startDateSymptoms.required' => 'Start date of symptoms is required.',
            'detailedSymptoms.required' => 'Detailed symptoms are required.',
        ];

        // Validate the request data
        $validated = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validated->fails()) {
            // Return validation errors as JSON
            return response()->json(['errors' => $validated->errors()], 422);
        }
        $validData = $validated->validated();


        // Return a successful response
        session()->put('medicalDetails', $validData );

        return response()->json([ 'message'=> ''], 200);
    }

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('id',  session('tele-consult-number'))->get()->last();
        $payment = new PaymentController();
        $secretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$secretKey], 200);
    }
    public function  saveConsultDetails(Request $request)
    {

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

        $validData= session('medicalDetails');
        $tr=Treatment::create([
            'user_email' => Auth::user()->email, 
            'pre_existing_health' => $validData['preExistingHealth'],
            'information_pre_existing_health' => $validData['informationPreExistingHealthYes']??null,
            'medications_regularly' => $validData['medicationsRegularly'],
            'medications_regularly_info' => $validData['medicationsRegularlyInfo']??null,
            'start_date_symptoms' => $validData['startDateSymptoms'],
            'detailed_symptoms' => $validData['detailedSymptoms'],
            'treatment_category' => $validData['treatment_category'],
            'request_status'=>"new request"
        ]);
     
    
        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id = session('tele-consult-number');
        $payment->customer_email = Auth::user()->email;
        $payment->treatment_id = $tr->id;    
        $payment->payment_status = "pending";    

        $payment->save();


        session()->forget(['payment_intent_id','tele-consult-number']);

        return response()->json([
            'redirect_url' => route('consult-category', ['messege' => "Your payment is pending when your request is fullfilled"])
        ]);


    }

}
