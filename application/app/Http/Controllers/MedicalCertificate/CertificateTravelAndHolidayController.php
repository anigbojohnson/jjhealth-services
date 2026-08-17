<?php

namespace App\Http\Controllers\MedicalCertificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MedicalCertificate;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CertificateTravelAndHolidayController extends Controller
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
            'dob' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:male,female,not say',
            'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
            'address' => 'required|string'
    ]);

    session()->put('personalDetails', $validatedData);

    return response()->json(['message' => 'success'], 200);

    }

    public function medicalDetails(Request $request)
    {

        $validatedData = $request->validate([     
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'string',
                'nullable'
            ],
            'medicalLetterReasons' => [
                'required',
                'not_in:noOption',
                'in:Serious illness,Acute injury,Hospitalization or surgery,Flare-ups of chronic condition,Mental health crisis,Destress due to bereavement,Infectious Disease,Pregnancy related complications,other',
            ],
            'detailedSymptoms' => [
            'required',
            'string',
            
            function ($attribute, $value, $fail) {
                if (str_word_count($value) < 20) {
                        $fail($attribute.' must have at least 20 words.');
                    }
                },
            ],
            'medicationsRegularlyInfo' => [
                'required_if:medicationsRegularly,Yes',
                'string',
                'nullable'
            ],
            'privacy'=>['required',
            function ($attribute, $value, $fail) {
                if ($value === 'noOption') {
                    $fail('Please select a valid option for privacy generic approach.');
                } 
            }
        ],
       'startDateSymptoms' => [
            'required',
            'date',
            function ($attribute, $value, $fail) {
                $date = Carbon::parse($value)->startOfDay();
                $tomorrow = Carbon::tomorrow()->startOfDay();
                if ($date->equalTo($tomorrow)) {
                    $fail($attribute . ' cannot be tomorrow.');
                }
            },
        ],
        ], [
            'medicalLetterReasons.not_in' => 'Please select a valid reason.',
            'medicalLetterReasons.in' => 'Please select a valid reason from the list.',
            'medicalLetterReasons.required' => 'The medical letter reason is required.',
            'currentStatus.required' => 'The current status is required.',  
            'currentStatus.in' => 'The selected current status is invalid.',


        ]);

        session()->put('medicalsDetails', $validatedData);

        $combinedData = [];

        $combinedDetails = [
            'personalDetails' => session('personalDetails'),
            'medicalDetails' => session('medicalsDetails')
        ];
        session()->put('combinedDetails',  $combinedDetails);
    
        return response()->json([
            'message' => 'success',
            'data' => $combinedDetails
        ], 200);

    }



public function storeMCDetails(Request $request)
{
    $seeking = "Travel and Holiday cancellation";

    $userData = session('personalDetails');
    $medicalData = session('medicalsDetails');

    try {

        DB::transaction(function () use (
            $userData,
            $medicalData,
            $seeking
        ) {

            // Create or update user
            User::updateOrCreate(
                ['email' => Auth::user()->email],
                [
                    'first_name'   => $userData['fname'],
                    'last_name'    => $userData['lname'],
                    'dob'          => $userData['dob'],
                    'gender'       => $userData['gender'],
                    'indigene'     => $userData['indigene'],
                    'address'      => $userData['address'],
                    'phone_number' => $userData['pnumber'],
                ]
            );

            // Create medical certificate
            $medicalCertificate = MedicalCertificate::create([
                'requestDate' =>
                    Carbon::now(),

                'user_email' =>
                    Auth::user()->email,

                'preExistingHealth' =>
                    $medicalData['preExistingHealth'],

                'medicationsRegularly' =>
                    $medicalData['medicationsRegularly'] ?? null,

                'seeking' =>
                    $seeking,

                'preExistingHealthInformation' =>
                    $medicalData['informationPreExistingHealthYes'] ?? null,

                'privacy' =>
                    $medicalData['privacy'] ?? null,

                'medicationsRegularlyInfo' =>
                    $medicalData['medicationsRegularlyInfo'] ?? null,

                'symptomsDetailed' =>
                    $medicalData['detailedSymptoms'] ?? null,

                'medicalLetterReasons' =>
                    $medicalData['medicalLetterReasons'] ?? null,

                'symptomsStartDate' =>
                    $medicalData['startDateSymptoms'] ?? null,

                'request_status' =>
                    'new request',
            ]);

            // Create payment
            Payment::create([
                'payment_id' =>
                    session('payment_intent_id'),

                'product_id' =>
                    session('credentials')->id,

                'customer_email' =>
                    Auth::user()->email,

                'mc_id' =>
                    $medicalCertificate->id,

                'payment_status' =>
                    'pending',
            ]);
        });

        // Only execute after successful transaction
        $data = [
            'first_name' =>
                $userData['fname'],

            'last_name' =>
                $userData['lname'],

            'solution_name' =>
                session('credentials')->solution_name . ' Medical Certificate',

            'cost' =>
                session('credentials')->cost,
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
                'certificate',
                [
                    'messege' =>
                        'Successful! please check your email for details'
                ]
            ),
        ]);

    } catch (\Throwable $e) {

        Log::error('Failed to save travel cancellation medical certificate', [
            'user_email' => Auth::user()->email,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' =>
                'We could not process your request at this time. Please try again.',
        ], 500);
    }
}
    

    public function getSecretKey(Request $request)
    {
        
        
        $payment = new PaymentController();
        $ecretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
}
