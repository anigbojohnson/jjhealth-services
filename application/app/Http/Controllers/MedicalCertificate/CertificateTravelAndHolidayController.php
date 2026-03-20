<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Payment as PaymentController;
use Carbon\Carbon;
use App\Models\Solutions;
use App\Models\User;
use App\Models\MedicalCertificate;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;


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

    public function storeMCDetails(Request $request){
    
        $seeking ="Travel and Holiday cancellation";

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


        
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth'],
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => $seeking??null, // Assuming seeking is part of the request
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'request_status'=>"new request"

        ]);

        $solutions = Solutions::where('solution_id', 'MC04')->first();

        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id =  $solutions->id;
        $payment->customer_email = Auth::user()->email;
        $payment->mc_id  =  $medicalCertificate->id;    
        $payment->payment_status = "pending";    

        $payment->save();


        session()->forget(['payment_intent_id']);

        return response()->json([
            'redirect_url' => route('certificate', ['messege' => "Your payment is pending when your request is fullfilled"])
        ]);
    }
    

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'MC04')->first();
        
        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
}
