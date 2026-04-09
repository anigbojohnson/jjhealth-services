<?php

namespace App\Http\Controllers\MedicalCertificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\PaymentController;
use App\Models\User;
use App\Models\MedicalCertificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\Storage;
use OpenTelemetry\API\Trace\TracerInterface;  // ← import this
use OpenTelemetry\API\Trace\StatusCode;


class CertificateCareController extends Controller
{
    //
    public function __construct(private TracerInterface $tracer) {}

    public function personalDetails(Request $request)
    {

    
        $span  = $this->tracer->spanBuilder('mc-carer-personal-details-validation')->startSpan();
        $scope = $span->activate();

        try {
            $span->setAttribute('user.email', Auth::user()->email);
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


            $span->setAttribute('validation.status', 'passed');
            session()->put('personalDetails', $validatedData);
            return response()->json(['message' => 'success'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $span->setAttribute('validation.status', 'failed');
            $span->setAttribute('validation.errors', json_encode($e->errors()));
            $span->setStatus(StatusCode::STATUS_ERROR, 'Validation failed');
            throw $e;

        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }

    }

    public function carerDetails(Request $request)
    {
        $span  = $this->tracer->spanBuilder('mc-carer-carer-details-validation')->startSpan();
        $scope = $span->activate();


        try {
            $span->setAttribute('user.email', Auth::user()->email ?? 'guest');

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
    $span->setAttribute('validation.status',    'passed');

    return response()->json(['message' => 'success'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $span->setAttribute('validation.status', 'failed');
            $span->setAttribute('validation.errors', json_encode($e->errors()));
            $span->setStatus(StatusCode::STATUS_ERROR, 'Validation failed');
            throw $e;

        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }

    }


    public function medicalDetails(Request $request)
    {
        $span  = $this->tracer->spanBuilder('mc-carer-medical-details-validation')->startSpan();
        $scope = $span->activate();

        try {
        $span->setAttribute('user.email', Auth::user()->email);

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

            $combinedDetails = [
                'personalDetails' => session('personalDetails'),
                'carerDetails'    => session('carerDetails'),
                'medicalDetails'  => session('medicalsDetails')
            ];
            session()->put('combinedDetails', $combinedDetails);

            return response()->json(['message' => 'success', 'data' => $combinedDetails], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $span->setAttribute('validation.status', 'failed');
            $span->setAttribute('validation.errors', json_encode($e->errors()));
            $span->setStatus(StatusCode::STATUS_ERROR, 'Validation failed');
            throw $e;

        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }

    }

    public function getSecretKey(Request $request)
    {        
        $span  = $this->tracer->spanBuilder('get-payment-secret-key')->startSpan();
        $scope = $span->activate();

        try {
        
        $span->setAttribute('user.email', Auth::user()->email);
        $payment = new PaymentController();
        $ecretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
                } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }
    }

 public function storeMCDetails(Request $request)
    {
        $span  = $this->tracer->spanBuilder('store-carer-medical-certificate')->startSpan();
        $scope = $span->activate();

        try {
            $span->setAttribute('user.email', Auth::user()->email);

            // ── step 1: save user details ──────────────────────────
            $userSpan  = $this->tracer->spanBuilder('update-or-create-user')->startSpan();
            $userScope = $userSpan->activate();

            try {
                $validatedData = session('personalDetails');

                $user = User::updateOrCreate(
                    ['email' => Auth::user()->email],
                    [
                        'first_name'   => $validatedData['fname'],
                        'last_name'    => $validatedData['lname'],
                        'dob'          => $validatedData['dob'],
                        'gender'       => $validatedData['gender'],
                        'indigene'     => $validatedData['indigene'],
                        'address'      => $validatedData['address'],
                        'phone_number' => $validatedData['pnumber']
                    ]
                );

                $userSpan->setAttribute('user.id',           $user->id);
                $userSpan->setAttribute('user.was_created',  $user->wasRecentlyCreated);

            } finally {
                $userScope->detach();
                $userSpan->end();
            }

            // ── step 2: create medical certificate ────────────────
            $mcSpan  = $this->tracer->spanBuilder('create-carer-medical-certificate')->startSpan();
            $mcScope = $mcSpan->activate();

            try {
                $validatedData = session('medicalsDetails');
                $validatedCare = session('carerDetails');

                $medicalCertificate = MedicalCertificate::create([
                    'requestDate'                  => Carbon::now(),
                    'user_email'                   => Auth::user()->email,
                    'preExistingHealth'            => $validatedData['preExistingHealth'],
                    'medicationsRegularly'         => $validatedData['medicationsRegularly'] ?? null,
                    'seeking'                      => session('credentials')->solution_name ?? null,
                    'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes'] ?? null,
                    'privacy'                      => $validatedData['privacy'] ?? null,
                    'medicationsRegularlyInfo'     => $validatedData['medicationsRegularlyInfo'] ?? null,
                    'symptomsDetailed'             => $validatedData['detailedSymptoms'] ?? null,
                    'validFrom'                    => $validatedCare['validFrom'] ?? null,
                    'medicalLetterReasons'         => $validatedData['medicalLetterReasons'] ?? null,
                    'symptomsStartDate'            => $validatedData['startDateSymptoms'] ?? null,
                    'currentStatus'                => $validatedData['currentStatus'] ?? null,
                    'validTo'                      => $validatedCare['validTo'] ?? null,
                    'careForSomeone'               => $validatedCare['careForSomeone'] ?? null,
                    'personCared'                  => $validatedCare['personCared'] ?? null,
                    'request_status'               => 'new request'
                ]);

                $mcSpan->setAttribute('mc.id',             $medicalCertificate->id);
                $mcSpan->setAttribute('mc.reason',         $validatedData['medicalLetterReasons'] ?? 'unknown');
                $mcSpan->setAttribute('mc.currentStatus',  $validatedData['currentStatus'] ?? 'unknown');

            } finally {
                $mcScope->detach();
                $mcSpan->end();
            }

            // ── step 3: save payment ───────────────────────────────
            $paySpan  = $this->tracer->spanBuilder('save-payment')->startSpan();
            $payScope = $paySpan->activate();

            try {
                $payment                 = new Payment();
                $payment->payment_id     = session('payment_intent_id');
                $payment->product_id     = session('credentials')->id;
                $payment->customer_email = Auth::user()->email;
                $payment->mc_id          = $medicalCertificate->id;
                $payment->payment_status = 'pending';
                $payment->save();

                $paySpan->setAttribute('payment.id',     $payment->payment_id);
                $paySpan->setAttribute('payment.status', 'pending');

            } finally {
                $payScope->detach();
                $paySpan->end();
            }

            // ── step 4: send confirmation email ───────────────────
            $mailSpan  = $this->tracer->spanBuilder('send-confirmation-email')->startSpan();
            $mailScope = $mailSpan->activate();

            try {
                $data = [
                    'first_name'    => $user->first_name,
                    'last_name'     => $user->last_name,
                    'solution_name' => session('credentials')->solution_name . ' Medical Certificate',
                    'cost'          => session('credentials')->cost,
                ];

                Mail::to(Auth::user()->email)->send(new VerifyConsultationMail($data));

                $mailSpan->setAttribute('email.sent_to', Auth::user()->email);
                $mailSpan->setAttribute('email.status',  'sent');

            } catch (\Throwable $e) {
                $mailSpan->recordException($e);
                $mailSpan->setStatus(StatusCode::STATUS_ERROR, 'Email failed');
                throw $e;

            } finally {
                $mailScope->detach();
                $mailSpan->end();
            }

            session()->forget(['payment_intent_id', 'credentials']);

            $span->setAttribute('store_mc.status', 'success');

            return response()->json([
                'redirect_url' => route('certificate', ['messege' => 'Successful! please check your email for details'])
            ]);

        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }
    }
}
