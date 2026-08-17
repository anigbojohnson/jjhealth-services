<?php

namespace App\Http\Controllers\MedicalCertificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MedicalCertificate;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CertificateWorkController extends Controller
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

    public function medicalDetails(Request $request)
    {
              
        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();
        $validFrom ="";
        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(3);
        $validatedData = $request->validate([
            'days_type' => 'required|in:single,multiple',
            'singleDay' => [
                'date',
                'nullable',
                'required_if:days_type,single'
             ],
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'max:255'
            ],
            'privacy'=>'required|in:,Yes Include specific health details and symptoms,No maintain generic approach for confidentiality',
            'medicationsRegularlyInfo' => [
                'required_if:medicationsRegularly,Yes',
                'max:255'
            ],
            'detailedSymptoms' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (str_word_count($value) < 20) {
                            $fail($attribute.' must have at least 20 words.');
                        }
                    },
                ],
                'validFrom' => [
                    'required_if:days_type,multiple',
                    'date',
                    function ($attribute, $value, $fail) use ($today, $tomorrow) {
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        if (!$date->equalTo($today) && !$date->equalTo($tomorrow)) {
                            $fail($attribute.' must be either today, or tomorrow.');
                        }
                        $validFrom = $date;
    
                    }
                ],
                'medicalLetterReasons'=>[
                    'required_if:work,sickLeave',
                ],
                'startDateSymptoms' => [
                        'required',
                        'date',
                        function ($attribute, $value, $fail) {
                            $date = Carbon::parse($value)->startOfDay();
                            $today = Carbon::today()->startOfDay();

                            // Check if the date is in the future
                            if ($date->isAfter($today)) {
                                $fail('start date symptoms cannot be in the future.');
                            }
                        },
                    ],
                    'currentStatus' => [
                    'required',
                    'in:ongoing,partially recovered,completely recovered',
                ],

                'validTo' => [
                    'required_if:days_type,multiple',
                    'date',
                    function ($attribute, $value, $fail) use ($validFrom) {
    
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        $validFromDate = \Carbon\Carbon::parse($validFrom)->startOfDay(); // Start date can be either today or tomorrow
                        $maxValidDate = $validFromDate->copy()->addDays(3)->endOfDay();   // Maximum of 2 days after 'validFrom'
                        
                        // Ensure 'validTo' is between 'validFrom' and the max valid date (2 days after 'validFrom')
                        if (!$date->between($validFromDate, $maxValidDate)) {
                            $fail('valid to must be any date between ' . $validFromDate->toFormattedDateString() . ' and ' . $maxValidDate->toFormattedDateString());
                        }
                    },
                    'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
                    'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'

                ],
                [
                    'medicationsRegularlyInfo.required_if' => 'This field is required ',
                    'informationPreExistingHealthYes.required_if' => 'This field is required',
                 
                ]
    ]);

    session()->put('medicalDetails', $validatedData);

    $combinedData = [];

    $combinedDetails = [
        'personalDetails' => session('personalDetails'),
        'workDetails' => session('workDetails'),
        'medicalDetails' => session('medicalDetails')
    ];
    session()->put('combinedDetails',  $combinedDetails);

    return response()->json([
        'message' => 'success',
        'data' => $combinedDetails
    ], 200);

    }

    public function workDetails(Request $request)
    {

        $validatedData = $request->validate([
         
            'jobDescription' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount < 5) {
                        $fail('The job description must contain at least 5 words.');
                    }
                },
            ],
            'symptomsRelationToJobs' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount < 5) {
                        $fail('The symptoms relation to jobs must contain at least 5 words.');
                    }
                },
            ],
        ]);
        session()->put('workDetails', $validatedData);

        return response()->json(['message' => 'success'], 200);

    }
   


public function storeMCDetails(Request $request)
{
    $personalData = session('personalDetails');
    $medicalData = session('medicalDetails');
    $workData = session('workDetails');

    $fileName = '';

    try {

        // Upload file once
        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');

            $fileName = time() . '_' . $file->getClientOriginalName();

            Storage::disk('s3')->putFileAs(
                'user-temp-file/' . Auth::user()->email,
                $file,
                $fileName,
                'public'
            );
        }

        DB::transaction(function () use (
            $personalData,
            $medicalData,
            $workData,
            $fileName
        ) {

            // Create/update user
            $user = User::updateOrCreate(
                ['email' => Auth::user()->email],
                [
                    'first_name' => $personalData['fname'],
                    'last_name'  => $personalData['lname'],
                    'dob'        => $personalData['dob'],
                    'gender'     => $personalData['gender'],
                    'indigene'   => $personalData['indigene'],
                    'address'    => $personalData['address'],
                ]
            );

            // Create medical certificate
            $medicalCertificate = MedicalCertificate::create([
                'requestDate' => Carbon::now(),
                'user_email' => Auth::user()->email,

                'preExistingHealth' =>
                    $medicalData['preExistingHealth'] ?? null,

                'medicationsRegularly' =>
                    $medicalData['medicationsRegularly'] ?? null,

                'seeking' =>
                    session('credentials')->solution_name ?? null,

                'preExistingHealthInformation' =>
                    $medicalData['informationPreExistingHealthYes'] ?? null,

                'privacy' =>
                    $medicalData['privacy'] ?? null,

                'medicationsRegularlyInfo' =>
                    $medicalData['medicationsRegularlyInfo'] ?? null,

                'symptomsDetailed' =>
                    $medicalData['detailedSymptoms'] ?? null,

                'validFrom' =>
                    $medicalData['validFrom'] ?? null,

                'medicalLetterReasons' =>
                    $medicalData['medicalLetterReasons'] ?? null,

                'symptomsStartDate' =>
                    $medicalData['startDateSymptoms'] ?? null,

                'currentStatus' =>
                    $medicalData['currentStatus'] ?? null,

                'validTo' =>
                    $medicalData['validTo'] ?? null,

                'jobDescription' =>
                    $medicalData['jobDescription'] ?? null,

                'fileUpload' =>
                    $fileName ?: null,

                'symptomsRelationToJobs' =>
                    $medicalData['symptomsRelationToJobs'] ?? null,

                'request_status' => 'new request',
            ]);

            // Create payment
            Payment::create([
                'payment_id' => session('payment_intent_id'),
                'product_id' => session('credentials')->id,
                'customer_email' => Auth::user()->email,
                'mc_id' => $medicalCertificate->id,
                'payment_status' => 'pending',
            ]);
        });

        // Transaction succeeded — send email
        $data = [
            'first_name' =>
                $personalData['fname'],

            'last_name' =>
                $personalData['lname'],

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

        // Remove S3 file if database transaction failed
        if ($fileName) {
            Storage::disk('s3')->delete(
                'user-temp-file/' .
                Auth::user()->email .
                '/' .
                $fileName
            );
        }

        Log::error('Failed to save medical certificate', [
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
