<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\MedicalCertificate;
use Carbon\Carbon;
use App\Models\Solutions;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;


class CertificateCareController extends Controller
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

    public function carerDetails(Request $request)
    {


        $messages = [
            'careForSomeone.required' => 'Please indicate if you are caring for someone within this period.',
            'careForSomeone.in' => 'Invalid selection. Please choose either Yes or No.',
            'careForSomeone.not_in' => 'Please select a valid option for caring for someone.',
        
            'personCared.required_if' => 'You need to specify who you are caring for when "Yes" is selected.',
        
 
        ];
        

        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();
        $validFrom ="";
        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(3);

        $validatedData = $request->validate([
            
            
      'careForSomeone' => [
                         'required',
                         'in:Yes,No',
                        ],
      'personCared' => [
                        'nullable',                             // Allows the field to be empty when not required
                        'required_if:careForSomeone,Yes',       // Required if careForSomeone is 'Yes'
                         'string',                               // Ensures the value is a string
      function ($attribute, $value, $fail) {
        if (request('careForSomeone') === 'Yes' && !in_array($value, ['child', 'parent', 'partner'])) {
                $fail('The person you are caring for must be either a child, parent, or partner.');
                }
            },
        ],
        'validFrom' => [
                'required',
                'nullable',
                'date',
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
            'validTo' => [
                'required',
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
    ],$messages);

    session()->put('carerDetails', $validatedData);
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
                                    'in:Headache or Migraine,Serious illness,Acute injury,Hospitalization or surgery,Flare-ups of chronic condition,Mental health crisis,Destress due to bereavement,Infectious Disease,Period pain,Pregnancy related complications,other',
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
        ], [
            'medicalLetterReasons.required' => 'The medical letter reason is required.',
            'currentStatus.required' => 'The current status is required.',  
            'medicalLetterReasons.in' => 'Please select a valid option for the medical letter reason.',
            'currentStatus.in' => 'The selected current status is invalid.',
            'informationPreExistingHealthYes.required_if'=>"The 'Pre-existing Health' field is required when 'Pre-existing Health' is set to Yes"

        ]);

        session()->put('medicalsDetails', $validatedData);

        $combinedData = [];

        $combinedDetails = [
            'personalDetails' => session('personalDetails'),
            'carerDetails' => session('carerDetails'),
            'medicalDetails' => session('medicalsDetails')
        ];
        session()->put('combinedDetails',  $combinedDetails);
    
        return response()->json([
            'message' => 'success',
            'data' => $combinedDetails
        ], 200);

    }

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'MC03')->latest('id')->first();
        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }

    public function storeMCDetails(Request $request){
        $seeking = "Carer's Leave Medical certificate";
    

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
        $validatedCare = session('carerDetails');


        
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth'],
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => $seeking??null, 
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'location' => $validatedStudies['yourStudiesPlace']??null,
            'validFrom' => $validatedCare['validFrom']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'currentStatus' => $validatedData['currentStatus']??null,
            'validTo' => $validatedCare['validTo']??null,
            'careForSomeone' => $validatedCare['careForSomeone']??null,
            'personCared' => $validatedCare['personCared']??null,
            'request_status'=>"new request"

        ]);
        $solutions = Solutions::where('solution_id', 'MC03')->latest('id')->first();

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
}
