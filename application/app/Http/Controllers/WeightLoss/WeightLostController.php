<?php
namespace App\Http\Controllers\WeightLoss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\WeightLoss;
use Illuminate\Support\Facades\Auth;
use App\Models\Solutions;
use App\Http\Controllers\Payment as PaymentController;
use Carbon\Carbon;
use App\Models\Payment;



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
        session()->put('medicalDetails', $validatedData);

            return response()->json(['message' => ''], 200);
  
        // Make the internal request to the payment.make route

    }
    public function getSecretKey(Request $request)
    {
        $solutions = Solutions::where('solution_id', 'like', 'WL%')->get()->last();        
        $payment = new PaymentController();
        $ecretKey = $payment->make($solutions);
        // Check the response and handle accordingly
        
        return response()->json([ 'secret_key'=>$ecretKey], 200);
    }


    public function  saveConsultDetails(Request $request)
    {

        $userData = session()->get('personalDetails');

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
        $fileName ="";
        if ($request->hasFile('fileUpload')) {
            // Get the file content
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = base64_encode(file_get_contents($file));

            $filePath = Storage::disk('s3')->putFileAs($file, $fileName);

            // Generate the URL to the file
            $fileUrl = Storage::disk('s3')->url($filePath);
        }

        $validData= session('medicalDetails');


        $consultationData = session()->get('consultationDetails');

        $wl= WeightLoss::create([
            'request_status'=>"new request",
             'user_email' => Auth::user()->email,
             'medication_used' => $validatedData['medication_used'],
             'diseases_pancreas_liver_kidneys' => $validatedData['diseases_pancreas_liver_kidneys'],
             'taking_insulin' => $validatedData['taking_insulin'],
             'allergic_reaction' => $validatedData['allergic_reaction'],
             'any_allergies' => $validatedData['any_allergies'],
             'pregnant' => $validatedData['pregnant'],
             'eating_disorder' => $validatedData['eating_disorder'],
             'cardiovascular_disease' => $validatedData['cardiovascular_disease'],
             'strong_pain_killers' => $validatedData['strong_pain_killers'],
             'severe_heart_failure' => $validatedData['severe_heart_failure'],
             'brain_tumour' => $validatedData['brain_tumour'],
             'bariatric_surgery' => $validatedData['bariatric_surgery'],
             'gastroparesis' => $validatedData['gastroparesis'],
             'requestReason' =>  $consultationData['requestReason'],
             'height' =>  $consultationData['height'],
             'weight' =>  $consultationData['weight'],
             'file_name' => $validatedData['medicalConditionImage']=='Yes' ? $fileName :null, // The original file name

         ]);
         $solutions = Solutions::where('solution_id', 'like', 'WL%')->get()->last();

         $payment = new Payment();
         $payment->payment_id = session('payment_intent_id');
         $payment->product_id =  $solutions->solution_id;
         $payment->customer_email = Auth::user()->email;
         $payment->weight_loss_id  = $wl->id;    
         $payment->payment_status = "pending";    
 
         $payment->save();
 
 
         session()->forget(['payment_intent_id']);
 
         return response()->json([
             'redirect_url' => route('weight-loss', ['messege' => "Your payment is pending when your request is fullfilled"])
         ]);



    }


}
