<?php
namespace App\Http\Controllers\Referrals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\SpecialistReferrals;
use Illuminate\Support\Facades\Auth;
use App\Models\Solutions;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SpecialistReferralsController extends Controller
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
            'medicalConditionImage' => 'required|in:Yes,No', // Ensures the value is required and must be either Yes or No
            'fileUpload' => 'required_if:medicalConditionImage,Yes|nullable|mimes:jpg,jpeg,png,pdf|max:5120', // File required only if 'Yes'
        ]);

        session()->put('consultationDetails', $validatedData);

        return response()->json(['message' => ''], 200);

    }

    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'SR01')->latest('id')->first();
        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }
   

public function saveConsultDetails(Request $request)
{

    $userData = session()->get('personalDetails');
    $consultationDetails = session()->get('consultationDetails');
    $category = Category::where('slug', 'specialist-referrals')
        ->firstOrFail();

    try {

        $fileName = "";

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
            $consultationDetails,
            $category,
            $fileName
        ) {

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

            $referral = Referral::create([
                'user_email'      => Auth::user()->email,
                'category_id'     => $category->id,
                'request_status'  => 'new request',
                'condition_image' => $consultationDetails['medicalConditionImage'] === 'Yes'
                                    ? $fileName
                                   : null,
                'request_reason'  => session('credentials')->solution_name
                                    . ': '
                                    . $consultationDetails['requestReason'],
            ]);

            $specialistReferral = $referral->specialist()->create([
                'referral_id' => $referral->id,
            ]);

            Payment::create([
                'payment_id'              => session('payment_intent_id'),
                'product_id'              => session('credentials')->id,
                'customer_email'          => Auth::user()->email,
                'specialist_referrals_id' => $specialistReferral->id,
                'payment_status'          => 'pending',
            ]);
        });

        session()->forget([
            'payment_intent_id',
            'credentials',
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route(
                'specialist-referral-home',
                [
                    'messege' => 'Successful! Please check your email for details.'
                ]
            ),
        ]);

    } catch (\Throwable $e) {

        Log::error('Failed to save specialist referral', [
            'user_email' => Auth::user()->email,
            'error'      => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
