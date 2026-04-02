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


class CertificateStudiesController extends Controller
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


    public function studiesDetails(Request $request)
    {

        $today = now()->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $validFrom = $request->validFrom;  // Get validFrom from the request
        $validatedData = $request->validate([
            'days_type' => 'required|in:single,multiple',
            'yourStudiesPlace' => 'required|string',
            'singleDay' => [
            'date',
            'nullable',
             'required_if:days_type,single'
             ],
            // Validation for validFrom
            'validFrom' => [
                'required_if:days_type,multiple',  // Only required if studies is 'sickLeave'
                'date',
                'nullable',
                function ($attribute, $value, $fail) use ($today, $tomorrow) {
                    if ($value) {
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        
                        // Check if validFrom is today or tomorrow
                        if (!$date->equalTo($today) && !$date->equalTo($tomorrow)) {
                            $fail('It must be either today or tomorrow.');
                        }
                    }
                }
            ],
            
            // Validation for validTo
            'validTo' => [
                'required_if:days_type,multiple',
                'date',
                'nullable',
                function ($attribute, $value, $fail) use ($validFrom, $today) {
                    if ($value && $validFrom) {
                        $validFromDate = \Carbon\Carbon::parse($validFrom)->startOfDay();
                        $validToDate = \Carbon\Carbon::parse($value)->startOfDay();
                        $maxValidDate = $today->copy()->addDays(3)->endOfDay();  // 3 days from today
                        
                        // Check if validTo is after or equal to validFrom
                        if ($validToDate->lt($validFromDate)) {
                            $fail('The valid to date must be after or equal to the valid from date.');
                        }
        
                        // Check if validTo is at most 3 days from today
                        if ($validToDate->gt($maxValidDate)) {
                            $fail('The valid to date must be at most three days from today.');
                        }
                    }
                }
            ]
        ],
        [
            // Custom error messages for required_if rule
            'validTo.required_if' => 'This field is required',
            'validFrom.required_if' => 'This field is required',
            'singleDay.required_if' => 'this field is required'
        ]);
        
        

    session()->put('studiesDetails', $validatedData);

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
        'in:Common cold or flu,Headache,Migraine,Back pain,Period pain,Anxiety, stress or depression,other',
        function ($attribute, $value, $fail) {
            if ($value === 'noOption') {
                $fail('Please select a valid option for the medical letter reason.');
            }
        }
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
            'currentStatus' => [
                'required',
                'in:ongoing,partially recovered,completely recovered',

                function ($attribute, $value, $fail) {
                    if ($value === 'noOption') {
                        
                        $fail('Please select a valid option for the medical letter reason.');
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
        'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
        'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'

        ], [
            'medicalLetterReasons.required' => 'The medical letter reason is required.',
            'currentStatus.required' => 'The current status is required.',  
            'medicalLetterReasons.in' => 'Please select a valid option for the medical letter reason.',
            'currentStatus.in' => 'The selected current status is invalid.',


        ]);

        unset($validatedData['fileUpload']);
        session()->put('medicalsDetails', $validatedData);


        $combinedData = [];

        $combinedDetails = [
            'personalDetails' => session('personalDetails'),
            'studiesDetails' => session('studiesDetails'),
            'medicalDetails' => session('medicalsDetails')
        ];
        session()->put('combinedDetails',  $combinedDetails);
    
        return response()->json([
            'message' => 'success',
            'data' => $combinedDetails
        ], 200);

    }

    public function storeMCDetails(Request $request){
    
        $validatedData = session('studiesDetails');

        $validatedData = session('personalDetails');

        $user = User::updateOrCreate(
            ['email' => Auth::user()->email], // Condition to find the user
            [
                'first_name' => $validatedData['fname'],
                'last_name' => $validatedData['lname'],
                'dob' => $validatedData['dob'],
                'gender' => $validatedData['gender'],
                'indigene' => $validatedData['indigene'],
                'address' => $validatedData['address'],
                'phone_number'=>$validatedData['pnumber']
            ]
        );

        $validatedData = session('medicalsDetails');
        $validatedStudies = session('studiesDetails');

        $fileName ="";
        if ($request->hasFile('fileUpload')) {
            // Get the file content
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = base64_encode(file_get_contents($file));
            $filePath = Storage::disk('s3')->putFileAs('user-temp-file/'. Auth::user()->email, $file, $fileName, 'public');

        }


        
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth'],
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => session('credentials')->solution_name.' Medical Certificate'??null, // Assuming seeking is part of the request
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'location' => $validatedStudies['yourStudiesPlace']??null,
            'validFrom' => $validatedStudies['validFrom']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'currentStatus' => $validatedData['currentStatus']??null,
            'validTo' => $validatedStudies['validTo']??null,
            'request_status'=>"new request",
            'fileUpload' => $fileName??null, 
            'days_type'=> $validatedStudies['days_type']??null,
            'singleDay'=> $validatedStudies['days_type']??null
        ]);


        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id = session('credentials')->id;
        $payment->customer_email = Auth::user()->email;
        $payment->mc_id  =  $medicalCertificate->id;    
        $payment->payment_status = "pending";    

        $payment->save();

        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->first_name,
            'solution_name' => session('credentials')->solution_name.' Medical Certificate',
            'cost' =>  session('credentials')->cost,
        ];


        Mail::to(Auth::user()->email)->send(new VerifyConsultationMail($data));

        session()->forget(['payment_intent_id','credentials']);

        return response()->json([
            'redirect_url' => route('certificate', ['messege' => "Successful! please check your email for details"])
        ]);
    }

    public function getSecretKey(Request $request)
    {
        
        $payment = new PaymentController();
        $ecretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
    
}
