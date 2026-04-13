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
use OpenTelemetry\API\Trace\TracerInterface;  // ← import this
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Metrics\MeterInterface;  
use App\Services\MetricsService;


class CertificateStudiesController extends Controller
{
    //
    public function __construct(private TracerInterface $tracer,   private MetricsService $metrics ) {}

    public function personalDetails(Request $request)
    {
        $span  = $this->tracer->spanBuilder('mc-studies-personal-details-validation')->startSpan();
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

            session()->put('personalDetails', $validatedData);
            $this->metrics->validationSucceeded('mc-studies-personal-details-validation');
            $span->setAttribute('validation.status', 'passed');
            return response()->json(['message' => 'success'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // record validation failure as a metric
            $this->metrics->validationFailed('mc-studies-personal-details-validation');
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


    public function studiesDetails(Request $request)
    {
        $span  = $this->tracer->spanBuilder('mc-studies-details-validation')->startSpan();
        $scope = $span->activate();
        
    try {
        $span->setAttribute('user.email', Auth::user()->email);

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
            $span->setAttribute('validation.status',    'passed');
            $this->metrics->validationSucceeded("mc-studies-details-validation");

            return response()->json(['message' => 'success'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            
            $this->metrics->validationFailed(
                'mc-studies-personal-details',
                $e->errors()
            );

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
         $span  = $this->tracer->spanBuilder('mc-studies-medical-details-validation')->startSpan();
         $scope = $span->activate();

      
        try {



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

        $span->setAttribute('validation.status', 'passed');
        $span->setAttribute('user.email', Auth::user()->email);
        $this->metrics->validationSucceeded("mc-studies-medical-details-validation");


    
        return response()->json([
            'message' => 'success',
            'data' => $combinedDetails
        ], 200);
           } catch (\Illuminate\Validation\ValidationException $e) {
            $span->setAttribute('validation.status', 'failed');
            $span->setAttribute('validation.errors', json_encode($e->errors()));
            $span->setStatus(StatusCode::STATUS_ERROR, 'Validation failed');
     
            $this->metrics->validationFailed(
                'mc-studies-medical-details-validation',
                $e->errors()
            );
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

   public function storeMCDetails(Request $request)
{
    $span  = $this->tracer->spanBuilder('store-studies-medical-certificate')->startSpan();
    $scope = $span->activate();

    $startTime = microtime(true);

    $validatedStudies = session('studiesDetails');
    $validatedData    = session('personalDetails');

    try {
        $email = Auth::user()->email;

        $span->setAttribute('user.email', $email);

        // ── step 1: user ─────────────────────────────
        $userSpan  = $this->tracer->spanBuilder('update-or-create-user')->startSpan();
        $userScope = $userSpan->activate();

        try {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'first_name' => $validatedData['fname'],
                    'last_name'  => $validatedData['lname'],
                    'dob'        => $validatedData['dob'],
                    'gender'     => $validatedData['gender'],
                    'indigene'   => $validatedData['indigene'],
                    'address'    => $validatedData['address'],
                    'phone_number' => $validatedData['pnumber']
                ]
            );

            $userSpan->setAttribute('user.id', $user->id);

        } finally {
            $userScope->detach();
            $userSpan->end();
        }

        // ── step 2: file upload ───────────────────────
        $fileName = null;

        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');

            try {
                $fileName = time().'_'.$file->getClientOriginalName();

                Storage::disk('s3')->putFileAs(
                    'user-temp-file/'.$email,
                    $file,
                    $fileName,
                    'public'
                );

                $this->metrics->fileUploadedSucceded();

            } catch (\Throwable $e) {
                $this->metrics->fileUploadFailed($e->getMessage());
                throw $e;
            }
        }

        $validatedData = session('medicalsDetails');
        // ── step 3: medical certificate ───────────────
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email'  => $email,
            'preExistingHealth' => $validatedData['preExistingHealth']??null,
            'medicationsRegularly' => $validatedData['medicationsRegularly'] ?? null,
            'seeking' => session('credentials')->solution_name.' Medical Certificate',
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes'] ?? null,
            'privacy' => $validatedData['privacy'] ?? null,
            'symptomsDetailed' => $validatedData['detailedSymptoms'] ?? null,
            'location' => $validatedStudies['yourStudiesPlace'] ?? null,
            'validFrom' => $validatedStudies['validFrom'] ?? null,
            'validTo' => $validatedStudies['validTo'] ?? null,
            'request_status' => "new request",
            'fileUpload' => $fileName,
        ]);

        $this->metrics->certificateCreated(
            session('credentials')->solution_name.' Medical Certificate',
        );

        // ── step 4: payment ───────────────────────────
        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id = session('credentials')->id;
        $payment->customer_email = $email;
        $payment->mc_id = $medicalCertificate->id;
        $payment->payment_status = "pending";
        $payment->save();

        $this->metrics->paymentSucceeded();

        // ── step 5: email ─────────────────────────────
        try {
            Mail::to($email)->send(new VerifyConsultationMail([
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'solution_name' => session('credentials')->solution_name,
                'cost' => session('credentials')->cost,
            ]));

            $this->metrics->emailSent();
            
        } catch (\Throwable $e) {
            $this->metrics->emailFailed($e->getMessage());
            throw $e;
        }

        // ── duration metric ───────────────────────────
        $duration = (microtime(true) - $startTime) * 1000;

        $this->metrics->storeMcDuration($duration, [
            'mc' => 'studies'
        ]);
        $this->metrics->storeMcSuccess('mc studies');

        $span->setAttribute('store_mc.status', 'success');

        session()->forget(['payment_intent_id','credentials']);

        return response()->json([
            'redirect_url' => route('certificate', [
                'messege' => "Successful! please check your email for details"
            ])
        ]);

    } catch (\Throwable $e) {
        $this->metrics->paymentFailed( $e->getMessage());
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

            // ✔ metric (no email inside metric!)
            $this->metrics->secretKeyRequested();

            $payment = new PaymentController();
            $secretKey = $payment->make();

            return response()->json([
                'secret_key' => $secretKey
            ], 200);

        } catch (\Throwable $e) {

            $this->metrics->secretKeyFailed($e->getMessage());

            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());

            throw $e;

        } finally {
            $scope->detach();
            $span->end();
        }
    }
}
