<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Solutions;
use App\Models\User;
use App\Models\MedicalCertificate;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Stripe\Stripe;




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
                    'required',
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
                    'required',
                    'date',
                    function ($attribute, $value, $fail) use ($validFrom) {
    
                        $date = \Carbon\Carbon::parse($value)->startOfDay();
                        $validFromDate = \Carbon\Carbon::parse($validFrom)->startOfDay(); // Start date can be either today or tomorrow
                        $maxValidDate = $validFromDate->copy()->addDays(3)->endOfDay();   // Maximum of 2 days after 'validFrom'
                        
                        // Ensure 'validTo' is between 'validFrom' and the max valid date (2 days after 'validFrom')
                        if (!$date->between($validFromDate, $maxValidDate)) {
                            $fail('valid to must be any date between ' . $validFromDate->toFormattedDateString() . ' and ' . $maxValidDate->toFormattedDateString());
                        }
                    }
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
     
            'work' => 'required|in:sickLeave,FitToReturn,startWork,adjustWork',
            'IAgree' => [
                'required_if:work,FitToReturn,startWork',
                'string',
                'max:255'
            ],
            'adjustmentsReasons' => [
                'required_if:work,adjustWork',
            ],
            [
                'adjustmentsReasons.required_if' => 'The adjustments reasons field is required ',
                'IAgree.required_if' => 'You must check the IAgree box',
                'dailyWorkActivities.required' => 'The daily work activities field is required.',
                'IAgree' => 'This button is required.'


            ]
        ]);
        session()->put('workDetails', $validatedData);

        return response()->json(['message' => 'success'], 200);

    }
    public function storeMCDetails(Request $request){
        $seeking = '';
    
        Stripe::setApiKey(config('services.stripe.secret'));
    
        $paymentIntent = PaymentIntent::retrieve(session('payment_intent_id'));


        $validatedData = session('workDetails');

        if ($validatedData['work'] == 'sickLeave') {
            $seeking = 'Sick leave from work';
        } elseif ($validatedData['work'] == 'FitToReturn') {
            $seeking = 'Fit to return to work';
        } elseif ($validatedData['work'] == 'startWork') {
            $seeking = 'Fit to start work';
        } elseif ($validatedData['work'] == 'adjustWork') {
            $seeking = 'Adjusting work duties';
        }
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
            ]
        );

        $validatedData = session('medicalDetails');
        $validatedWork = session('workDetails');

        try {
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth']??null,
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => $seeking??null, // Assuming seeking is part of the request
            'IAgree' => $validatedWork['IAgree']??null,
            'adjustmentsReasons' => $validatedWork['adjustmentsReasons']??null,
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'validFrom' => $validatedData['validFrom']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'currentStatus' => $validatedData['currentStatus']??null,
            'validTo' => $validatedData['validTo']??null,
            'request_status'=>"new request"
        ]);
     
            $solutions = Solutions::where('solution_id', 'like', 'MC%')->get()->last();
    
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
    
        } catch (\Stripe\Exception\CardException $e) {
            if ($e->getError()->code === 'insufficient_funds') {
                return response()->json(['error' => 'The payment failed due to insufficient funds.']);
            } else {
                return response()->json(['error' => 'Payment failed: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }


    }

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'MC01')->latest('id')->first();
        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
}
