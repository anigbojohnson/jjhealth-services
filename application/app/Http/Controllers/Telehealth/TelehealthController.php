<?php

namespace App\Http\Controllers\Telehealth;
use Illuminate\Support\Facades\Validator;
use App\Models\Treatment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

use App\Models\User;
use App\Models\Solutions;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $payment = new PaymentController();
        $secretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$secretKey], 200);
    }
   


public function saveConsultDetails(Request $request)
{
    $userData = session()->get('personalDetails');
    $validData = session()->get('medicalDetails');

    try {

        DB::transaction(function () use ($userData, $validData) {

            // Create or update user
            User::updateOrCreate(
                ['email' => Auth::user()->email],
                [
                    'first_name'   => $userData['fname'],
                    'last_name'    => $userData['lname'],
                    'phone_number' => $userData['pnumber'],
                    'dob'          => $userData['dob'],
                    'gender'       => $userData['gender'],
                    'indigene'     => $userData['indigene'],
                    'address'      => $userData['address'],
                ]
            );

            // Create treatment
            $treatment = Treatment::create([
                'user_email'                    => Auth::user()->email,
                'pre_existing_health'            => $validData['preExistingHealth'],
                'information_pre_existing_health' => $validData['informationPreExistingHealthYes'] ?? null,
                'medications_regularly'         => $validData['medicationsRegularly'],
                'medications_regularly_info'    => $validData['medicationsRegularlyInfo'] ?? null,
                'start_date_symptoms'           => $validData['startDateSymptoms'],
                'detailed_symptoms'             => $validData['detailedSymptoms'],
                'treatment_category'            => $validData['treatment_category'],
                'request_status'                => 'new request',
            ]);

            // Create payment
            Payment::create([
                'payment_id'   => session('payment_intent_id'),
                'product_id'   => session('credentials')->id,
                'customer_email' => Auth::user()->email,
                'treatment_id' => $treatment->id,
                'payment_status' => 'pending',
            ]);
        });

        // Transaction succeeded, so send email
        $data = [
            'first_name'   => $userData['fname'],
            'last_name'    => $userData['lname'],
            'solution_name' => session('credentials')->solution_name,
            'cost'         => session('credentials')->cost,
        ];

        Mail::to(Auth::user()->email)
            ->send(new VerifyConsultationMail($data));

        session()->forget([
            'payment_intent_id',
            'credentials',
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route(
                'consult-category',
                [
                    'messege' => 'Successful! please check your email for details'
                ]
            ),
        ]);

    } catch (\Throwable $e) {

        Log::error('Failed to save treatment consultation', [
            'user_email' => Auth::user()->email,
            'error'      => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'We could not process your request at this time. Please try again.',
        ], 500);
    }
 }
}
