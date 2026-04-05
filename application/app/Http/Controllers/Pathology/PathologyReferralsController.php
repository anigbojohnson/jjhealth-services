<?php

namespace App\Http\Controllers\Pathology;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;
use App\Models\PathologyReferrals;
use App\Models\Solutions;
use App\Http\Controllers\Payment\PaymentController as PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyConsultationMail;
use Illuminate\Support\Facades\Storage;

class PathologyReferralsController extends Controller
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


    public function consultationDetails(Request $request)
        {
        $rules = [
                'selected_tests'        => 'required|array|min:1',
                'medicalConditionImage' => 'required|in:Yes,No',
                'fileUpload'            => 'required_if:medicalConditionImage,Yes|nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            ];

 


        // Define custom error messages
    $messages = [
        'medicalConditionImage.required' => 'Please indicate whether you would like to upload a photo of your condition.',
        'medicalConditionImage.in'       => 'The selected option for uploading a photo must be either Yes or No.',
        'fileUpload.required_if'         => 'Please upload a photo of your condition since you selected Yes.',
        'fileUpload.file'                => 'The uploaded file is not valid. Please try again.',
        'fileUpload.mimes'               => 'The photo must be a valid image file. Accepted formats: JPG, JPEG, PNG, or WEBP.',
        'fileUpload.max'                 => 'The photo size must not exceed 5MB. Please upload a smaller file.',
        'selected_tests.required'        => 'Please select at least one test before proceeding.',

    ];

        // Validate the request data
        $validated = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validated->fails()) {
            // Return validation errors as JSON
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $validData = $validated->validated();

        unset($validData['fileUpload']);

        // Return a successful response
        session()->put('medicalDetails', $validData );

        return response()->json([ 'message'=> ''], 200);
    }

    public function getSecretKey(Request $request)
    {
        $payment = new PaymentController();
        $secretKey = $payment->make();
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$secretKey], 200);
    }

    public function  saveConsultDetails(Request $request)
    {

        $userData = session()->get('personalDetails');


        $fileName ="";
        if ($request->hasFile('fileUpload')) {
            // Get the file content
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = base64_encode(file_get_contents($file));
            $filePath = Storage::disk('s3')->putFileAs('user-temp-file/'. Auth::user()->email, $file, $fileName, 'public');

        }

        $user = User::updateOrCreate(
            ['email' => Auth::user()->email], // Condition to find the user
            [
                'first_name' => $userData['fname'],
                'last_name' => $userData['lname'],
                'phone_number' => $userData['pnumber'],
                'dob' => $userData['dob'],
                'gender' => $userData['gender'],
                'indigene' =>$userData['indigene'],
                'address' => $userData['address'],
            ]
        );

        $validData= session('medicalDetails');
        $pr=PathologyReferrals::create([
            'user_email' => Auth::user()->email, 
            'imageUpload' =>   $fileName,
            'solution_available_testing' =>  $validData['selected_tests'],
            'requestReason' => session('credentials')->solution_name,
            'request_status'=>"new request"
        ]);
     
    
        $payment = new Payment();
        $payment->payment_id = session('payment_intent_id');
        $payment->product_id = session('credentials')->id;
        $payment->customer_email = Auth::user()->email;
        $payment->pathology_referral_id = $pr->id;    
        $payment->payment_status = "pending";    

        $payment->save();
        $data = [
        'first_name' => $userData['fname'],
        'last_name' => $userData['lname'],
        'solution_name' => session('credentials')->solution_name,
        'cost' =>  session('credentials')->cost,
        ];

        $fileName ="";
        if ($request->hasFile('fileUpload')) {
            // Get the file content
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = base64_encode(file_get_contents($file));
            $filePath = Storage::disk('s3')->putFileAs('user-temp-file/'. Auth::user()->email, $file, $fileName, 'public');

        }

        Mail::to(Auth::user()->email)->send(new VerifyConsultationMail($data));

        session()->forget(['payment_intent_id','credentials']);

        return response()->json([
            'redirect_url' => route('pathology.select', ['messege' => "Successful! please check your email for details"])
        ]);

    }

}
