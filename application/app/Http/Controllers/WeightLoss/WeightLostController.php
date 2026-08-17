<?php
namespace App\Http\Controllers\WeightLoss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\WeightLoss;
use Illuminate\Support\Facades\Auth;
use App\Models\Solutions;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeightLostController extends Controller
{


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
        $validatedData = $request->validate([      
            'requestReason' => 'required|string|max:255',
            'height' => 'required|numeric|min:50', 
            'weight' => 'required|numeric|min:20', 
    ]);
    session()->put('consultationDetails', $validatedData);


    return response()->json(['message' => 'success'], 200);

    }

    public function medicalDetails(Request $request)
    {

        $validatedData = $request->validate([      
            'medication_used' => 'required|in:Yes,No',
            'diseases_pancreas_liver_kidneys' => 'required|in:Yes,No',
            'taking_insulin' => 'required|in:Yes,No',
            'allergic_reaction' => 'required|in:Yes,No',
            'any_allergies' => 'required|in:Yes,No',
            'pregnant' => 'required|in:Yes,No',
            'eating_disorder' => 'required|in:Yes,No',
            'cardiovascular_disease' => 'required|in:Yes,No',
            'strong_pain_killers' => 'required|in:Yes,No',
            'severe_heart_failure' => 'required|in:Yes,No',
            'brain_tumour' => 'required|in:Yes,No',
            'bariatric_surgery' => 'required|in:Yes,No',
            'gastroparesis' => 'required|in:Yes,No',
            'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
            'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'

        ]);

        if($validatedData['diseases_pancreas_liver_kidneys']=="Yes"){
            return response()->json(['message' => 'invalid'], 200);

        }
        $validatedData['fileUpload'] = "";
        session()->put('medicalDetails', $validatedData);

            return response()->json(['message' => ''], 200);
  
        // Make the internal request to the payment.make route

    }
    public function getSecretKey(Request $request)
    {
        $payment = new PaymentController();
        $ecretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }



public function saveConsultDetails(Request $request)
{
    $userData = session()->get('personalDetails');
    $validatedData = session('medicalDetails');
    $consultationData = session()->get('consultationDetails');

    $fileName = '';

    try {

        // Upload file
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
            $userData,
            $validatedData,
            $consultationData,
            $fileName
        ) {

            // Create/update user
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

            // Create weight-loss request
            $weightLoss = WeightLoss::create([
                'request_status' => 'new request',
                'user_email' => Auth::user()->email,

                'medication_used' =>
                    $validatedData['medication_used'],

                'diseases_pancreas_liver_kidneys' =>
                    $validatedData['diseases_pancreas_liver_kidneys'],

                'taking_insulin' =>
                    $validatedData['taking_insulin'],

                'allergic_reaction' =>
                    $validatedData['allergic_reaction'],

                'any_allergies' =>
                    $validatedData['any_allergies'],

                'pregnant' =>
                    $validatedData['pregnant'],

                'eating_disorder' =>
                    $validatedData['eating_disorder'],

                'cardiovascular_disease' =>
                    $validatedData['cardiovascular_disease'],

                'strong_pain_killers' =>
                    $validatedData['strong_pain_killers'],

                'severe_heart_failure' =>
                    $validatedData['severe_heart_failure'],

                'brain_tumour' =>
                    $validatedData['brain_tumour'],

                'bariatric_surgery' =>
                    $validatedData['bariatric_surgery'],

                'gastroparesis' =>
                    $validatedData['gastroparesis'],

                'requestReason' =>
                    $consultationData['requestReason'],

                'height' =>
                    $consultationData['height'],

                'weight' =>
                    $consultationData['weight'],

                'file_name' =>
                    $validatedData['medicalConditionImage'] === 'Yes'
                        ? $fileName
                        : null,
            ]);

            // Create payment
            Payment::create([
                'payment_id' => session('payment_intent_id'),
                'product_id' => session('credentials')->id,
                'customer_email' => Auth::user()->email,
                'weight_loss_id' => $weightLoss->id,
                'payment_status' => 'pending',
            ]);
        });

        // Only execute after transaction successfully commits
        $data = [
            'first_name' => $userData['fname'],
            'last_name' => $userData['lname'],
            'solution_name' => session('credentials')->solution_name,
            'cost' => session('credentials')->cost,
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
                'weight-loss',
                [
                    'messege' => 'Successful! please check your email for details'
                ]
            ),
        ]);

    } catch (\Throwable $e) {

        // Clean up S3 file if database transaction failed
        if ($fileName) {
            Storage::disk('s3')->delete(
                'user-temp-file/' . Auth::user()->email . '/' . $fileName
            );
        }

        Log::error('Failed to save weight loss request', [
            'user_email' => Auth::user()->email,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'We could not process your request at this time. Please try again.',
        ], 500);
    }
}
  
}
