<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalCertificate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;


class CertificateController extends Controller
{
    //
    public function showForm()
    {
        return view('form');
    }

    public function work(Request $request)
    {

        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(14);

        $validatedData = $request->validate([
            'fname' => 'required|string',
            'lname' => 'required|string',
            'dob' => 'required|date|before:-18 years',
            'pnumber' => [
                'required',
                'regex:/^(?:\+61|0)[2-478](?:[ -]?[0-9]){8}$/'
            ],
            'gender' => 'required|in:male,female,not say',
            'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
            'address' => 'required|string',
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',
            'work' => 'required|in:sickLeave,FitToReturn,startWork,adjustWork',
            'IAgree' => [
                'required_if:work,FitToReturn,startWork',
                'string',
                'max:255'
            ],
            'adjustmentsReasons' => [
                'required_if:work,adjustWork',
                'string',
            ],
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'string',
                'max:255'
            ],
            'privacy'=>'required|in:,Yes Include specific health details and symptoms,No maintain generic approach for confidentiality',
            'medicationsRegularlyInfo' => [
                'required_if:medicationsRegularly,Yes',
                'string',
                'max:255'
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
            'placeYouWork'=>'required|string',
            'dailyWorkActivities'=>'required|string',
            'validFrom' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($yesterday, $today, $tomorrow) {
                    $date = \Carbon\Carbon::parse($value)->startOfDay();
                    if (!$date->equalTo($yesterday) && !$date->equalTo($today) && !$date->equalTo($tomorrow)) {
                        $fail($attribute.' must be either yesterday, today, or tomorrow.');
                    }
                }
            ],
            'medicalLetterReasons'=>[
                'required_if:work,sickLeave',
                'string',
            ],
            'validTo' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $date = \Carbon\Carbon::parse($value)->startOfDay();
                    $today = \Carbon\Carbon::today()->startOfDay();
                    $maxValidDate = \Carbon\Carbon::today()->addDays(3)->endOfDay();
        
                    if (!$date->between($today, $maxValidDate)) {
                        $fail($attribute.' must be atmost 3 days from today.');
                    }
                }
            ],
        ]);


        $seeking = '';

        if ($validatedData['work'] == 'sickLeave') {
            $seeking = 'Sick leave from work';
        } elseif ($validatedData['work'] == 'FitToReturn') {
            $seeking = 'Fit to return to work';
        } elseif ($validatedData['work'] == 'startWork') {
            $seeking = 'Fit to start work';
        } elseif ($validatedData['work'] == 'adjustWork') {
            $seeking = 'Adjusting work duties';
        }

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


        
        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth']??null,
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => $seeking??null, // Assuming seeking is part of the request
            'IAgree' => $validatedData['IAgree']??null,
            'adjustmentsReasons' => $validatedData['adjustmentsReasons']??null,
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'location' => $validatedData['placeYouWork']??null,
            'dailyWorkActivities' => $validatedData['dailyWorkActivities']??null,
            'validFrom' => $validatedData['validFrom']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'symptomsEndDate' => $validatedData['endDateSymptoms']??null,
            'validTo' => $validatedData['validTo']??null,
        ]);
        return response()->json(['message' => 'Form submitted successfully!','id'=> $medicalCertificate->id], 200);

    }

    public function studies(Request $request)
    {

        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(14);

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
            'address' => 'required|string',
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',
            'studies' => 'required|in:sickLeave,resumeStudies',
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'string'
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
            
            ],
            'privacy'=>'required|in:,Yes Include specific health details and symptoms,No maintain generic approach for confidentiality',
            'yourStudiesPlace'=>'required|string',
            'validFrom' => [
                'required_if:studies,sickLeave',
                'date',
                function ($attribute, $value, $fail) use ($yesterday, $today, $tomorrow) {
                    $date = \Carbon\Carbon::parse($value)->startOfDay();
                    if (!$date->equalTo($yesterday) && !$date->equalTo($today) && !$date->equalTo($tomorrow)) {
                        $fail($attribute.' must be either yesterday, today, or tomorrow.');
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
        'endDateSymptoms' => [
            'date',
            function ($attribute, $value, $fail) use ($request) {
                $startDate = Carbon::parse($request->input('startDateSymptoms'))->startOfDay();
                $endDate = Carbon::parse($value)->startOfDay();
                $tomorrow = Carbon::tomorrow()->startOfDay();

                if ($endDate->equalTo($tomorrow)) {
                    $fail($attribute . ' cannot be tomorrow.');
                }

                if ($endDate->lt($startDate)) {
                    $fail($attribute . ' must be after the start date.');
                }
            },
        ],
            'validTo' => [
                'required_if:studies,sickLeave',
                'date',
                function ($attribute, $value, $fail) {
                    $date = \Carbon\Carbon::parse($value)->startOfDay();
                    $today = \Carbon\Carbon::today()->startOfDay();
                    $maxValidDate = \Carbon\Carbon::today()->addDays(3)->endOfDay();
        
                    if (!$date->between($today, $maxValidDate)) {
                        $fail($attribute.' must be atmost 3 days from today.');
                    }
                }
            ],
        ]);


        $user = User::updateOrCreate(
            ['email' => Auth::user()->email], // Condition to find the user
            [
                'first_name' => $validatedData['fname'],
                'last_name' => $validatedData['lname'],
                'phone_number' => $validatedData['pnumber'],
                'dob' => $validatedData['dob'],
                'gender' => $validatedData['gender'],
                'indigene' => $validatedData['indigene'],
                'address' => $validatedData['address'],
            ]
        );


        $seeking = '';

        if ($validatedData['studies'] == 'sickLeave') {
            $seeking = 'Sick leave from studies';
        } elseif ($validatedData['studies'] == 'resumeStudies') {
            $seeking = 'Fit to resume studies';
        } 

        $medicalCertificate = MedicalCertificate::create([
            'requestDate' => Carbon::now(),
            'user_email' => Auth::user()->email,
            'preExistingHealth' => $validatedData['preExistingHealth']??null,
            'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
            'seeking' => $seeking??null, // Assuming seeking is part of the request
            'IAgree' => $validatedData['IAgree']??null,
            'adjustmentsReasons' => $validatedData['adjustmentsReasons']??null,
            'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
            'privacy' => $validatedData['privacy']??null,
            'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
            'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
            'location' => $validatedData['yourStudiesPlace']??null,
            'dailyWorkActivities' => $validatedData['dailyWorkActivities']??null,
            'validFrom' => $validatedData['validFrom']??null,
            'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
            'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
            'symptomsEndDate' => $validatedData['endDateSymptoms']??null,
            'validTo' => $validatedData['validTo']??null,
        ]);

        return response()->json(['message' => 'Form submitted successfully!','id'=> $medicalCertificate->id], 200);

    }


    public function travelAndHoliday(Request $request)
    {

        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(14);

        $validatedData = $request->validate([

            'fname' => 'required|string',
            'lname' => 'required|string',
            'dob' => 'required|date|before:-18 years',
            'gender' => 'required|in:male,female,not say',
            'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
            'address' => 'required|string',
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',      
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'string',
            ],
            'medicationsRegularlyInfo' => [
                'required_if:medicationsRegularly,Yes',
                'string',
            ],
            'privacy'=>'required|in:,Yes Include specific health details and symptoms,No maintain generic approach for confidentiality',
            'detailedSymptoms' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (str_word_count($value) < 20) {
                            $fail($attribute.' must have at least 20 words.');
                        }
                    },
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
        'endDateSymptoms' => [
            'date',
            function ($attribute, $value, $fail) use ($request) {
                $startDate = Carbon::parse($request->input('startDateSymptoms'))->startOfDay();
                $endDate = Carbon::parse($value)->startOfDay();
                $tomorrow = Carbon::tomorrow()->startOfDay();

                if ($endDate->equalTo($tomorrow)) {
                    $fail($attribute . ' cannot be tomorrow.');
                }

                if ($endDate->lt($startDate)) {
                    $fail($attribute . ' must be after the start date.');
                }
            },
        ],
    ]);

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

    $seeking ="Travel and Holiday cancellation";
    $medicalCertificate = MedicalCertificate::create([
        'requestDate' => Carbon::now(),
        'user_email' => Auth::user()->email,
        'preExistingHealth' => $validatedData['preExistingHealth']??null,
        'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
        'seeking' => $seeking??null, // Assuming seeking is part of the request
        'IAgree' => $validatedData['IAgree']??null,
        'adjustmentsReasons' => $validatedData['adjustmentsReasons']??null,
        'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
        'privacy' => $validatedData['privacy']??null,
        'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
        'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
        'dailyWorkActivities' => $validatedData['dailyWorkActivities']??null,
        'validFrom' => $validatedData['validFrom']??null,
        'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
        'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
        'symptomsEndDate' => $validatedData['endDateSymptoms']??null,
        'validTo' => $validatedData['validTo']??null,
    ]);
    
    return response()->json(['message' => 'Form submitted successfully!','id'=> $medicalCertificate->id], 200);

    }



    
    public function careMC(Request $request){

        $today = now()->startOfDay();

        // Define custom validation rule for 'validFrom'
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        // Define the expected 'validTo' date
        $validToDate = $today->copy()->addDays(14);

        $validatedData = $request->validate([
            
            'fname' => 'required|string',
            'lname' => 'required|string',
            'dob' => 'required|date|before:-18 years',
            'gender' => 'required|in:male,female,not say',
            'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
            'address' => 'required|string',
            'preExistingHealth' => 'required|in:,Yes,No',
            'medicationsRegularly' => 'required|in:,Yes,No',      
            'informationPreExistingHealthYes' => [
                'required_if:preExistingHealth,Yes',
                'string',
            ],
            'careForSomeone' => 'required|in:,Yes,No',
            'personCared' => [
                'required_if:careForSomeone,Yes',
                'string',
                'in:child,parent,partner'
            ],
            'medicationsRegularlyInfo' => [
                'required_if:medicationsRegularly,Yes',
                'string',
            ],
            'privacy'=>'required|in:,Yes Include specific health details and symptoms,No maintain generic approach for confidentiality',
            'detailedSymptoms' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (str_word_count($value) < 20) {
                            $fail($attribute.' must have at least 20 words.');
                        }
                    },
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
        'endDateSymptoms' => [
            'date',
            function ($attribute, $value, $fail) use ($request) {
                $startDate = Carbon::parse($request->input('startDateSymptoms'))->startOfDay();
                $endDate = Carbon::parse($value)->startOfDay();
                $tomorrow = Carbon::tomorrow()->startOfDay();

                if ($endDate->equalTo($tomorrow)) {
                    $fail($attribute . ' cannot be tomorrow.');
                }

                if ($endDate->lt($startDate)) {
                    $fail($attribute . ' must be after the start date.');
                }
            },
        ],
        'validFrom' => [
            'required_if:studies,sickLeave',
            'date',
            function ($attribute, $value, $fail) use ($yesterday, $today, $tomorrow) {
                $date = \Carbon\Carbon::parse($value)->startOfDay();
                if (!$date->equalTo($yesterday) && !$date->equalTo($today) && !$date->equalTo($tomorrow)) {
                    $fail($attribute.' must be either yesterday, today, or tomorrow.');
                }
            }
        ],
        'validTo' => [
            'required',
            'date',
            function ($attribute, $value, $fail) {
                $date = \Carbon\Carbon::parse($value)->startOfDay();
                $today = \Carbon\Carbon::today()->startOfDay();
                $maxValidDate = \Carbon\Carbon::today()->addDays(3)->endOfDay();
    
                if (!$date->between($today, $maxValidDate)) {
                    $fail($attribute.' must be atmost 3 days from today.');
                }
            }
        ],
    ]);

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

    $seeking ="Travel and Holiday cancellation";
    $medicalCertificate = MedicalCertificate::create([
        'requestDate' => Carbon::now(),
        'user_email' => Auth::user()->email,
        'preExistingHealth' => $validatedData['preExistingHealth']??null,
        'medicationsRegularly' => $validatedData['medicationsRegularly']??null,
        'seeking' => $seeking??null, // Assuming seeking is part of the request
        'IAgree' => $validatedData['IAgree']??null,
        'adjustmentsReasons' => $validatedData['adjustmentsReasons']??null,
        'preExistingHealthInformation' => $validatedData['informationPreExistingHealthYes']??null,
        'privacy' => $validatedData['privacy']??null,
        'medicationsRegularlyInfo' => $validatedData['medicationsRegularlyInfo']??null,
        'symptomsDetailed' => $validatedData['detailedSymptoms']??null,
        'dailyWorkActivities' => $validatedData['dailyWorkActivities']??null,
        'validFrom' => $validatedData['validFrom']??null,
        'medicalLetterReasons' => $validatedData['medicalLetterReasons']??null,
        'symptomsStartDate' => $validatedData['startDateSymptoms']??null,
        'symptomsEndDate' => $validatedData['endDateSymptoms']??null,
        'validTo' => $validatedData['validTo']??null,
        'careForSomeone' => $validatedData['careForSomeone']??null,
        'personCared' => $validatedData['personCared']??null,

    ]);
    
    return response()->json(['message' => 'Form submitted successfully!','id'=> $medicalCertificate->id,'action'=>"MC"], 200);

    }

    
}
