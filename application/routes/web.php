<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ForgottenPasswordController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\Auth\CertificateController;
use App\Http\Controllers\Auth\PaymentController;
use App\Http\Controllers\Auth\SpecialistReferralsController;
use App\Http\Controllers\Auth\AuthMicrosoftLoginController;
use App\Http\Controllers\Auth\MedicationController;

use App\Http\Controllers\Auth\AuthGoogleLoginController;
use App\Http\Controllers\Auth\AuthGoogleDriveController;
use App\Http\Controllers\Auth\WeightLostController;

use App\Http\Controllers\Auth\TelehealthController;
use App\Http\Controllers\Auth\CertificateWorkController;

use App\Http\Controllers\Auth\CertificateStudiesController;
use App\Http\Controllers\Auth\CertificateTravelAndHolidayController;

use App\Http\Controllers\Auth\CertificateCareController;
use App\Models\Solutions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


Route::get('/search-solutions', function (Request $request) {
    $query = $request->input('query');

    // Perform the search only if there is a query
    $solutions = Solutions::select('solutions.*')
        ->whereIn('id', function ($subQuery) {
            $subQuery->select(DB::raw('MAX(id)'))
                  ->from('solutions')
                  ->groupBy('solution_id');
        })
        ->where('category', 'Telehealth Consult')  // Filter by category
        ->when($query, function ($subQuery) use ($query) {
            $subQuery->where('solution_name', 'LIKE', "%{$query}%")
                     ->orWhere('description', 'LIKE', "%{$query}%");
        })
        ->get();
    
    // If no results found or no search query, get default solutions
    if ($solutions->isEmpty() || !$query) {
        $solutions = Solutions::select('solutions.*')
            ->whereIn('id', function ($subQuery) {
                $subQuery->select(DB::raw('MAX(id)'))
                      ->from('solutions')
                      ->groupBy('solution_id');
            })
            ->where('category', 'Telehealth Consult')  // Filter by category
            ->get();
    
        $message = 'No results found for your search, showing default categories.';
    } else {
        $message = ''; // Empty message if results are found
    }
    
    // Pass the solutions and message to the view
    return view('auth.doctor-consult-category', compact('solutions', 'message'));
    
})->name('search-solutions');

Route::get('/', function () {
    return view('auth.home');
})->name('/');


Route::get('/weight-loss', function () {
    return view('auth.weight-lost-home');
})->name('weight-loss');


Route::get('/specialist_referrals', function () {
    return view('auth.specialist-referrals-home');
})->name('specialist_referrals');


Route::get('/weight-loss-consultation/{param}/{action}', function ($param, $action) {
    if (Auth::check()) {
        $user = Auth::user();
        return view('auth.'.$action, ['param' => $param,'user'=>$user]);
    } else {
        session()->put('action', $action);
        session()->put('param', $param);

        return view('auth.not-registered-or-login', ['param' => $param,'action'=>$action]);
    }
})->name('weight-loss-consultation');


Route::get('/specialist-referrals-request/{param}/{action}', function ($param, $action) {
    if (Auth::check()) {
        $user = Auth::user();
        return view('auth.'.$action, ['param' => $param,'user'=>$user]);
    } else {
        session()->put('action', $action);
        session()->put('param', $param);
        return view('auth.not-registered-or-login', ['param' => $param,'action'=>$action]);
    }
})->name('specialist-referrals-request');




Route::get('/certificate', function () {

    $solutions  = Solutions::select('solutions.*')
    ->whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
              ->from('solutions')
              ->groupBy('solution_id');
    })
    ->where('category', 'mitigating circumstance') 
    ->get();

    return view('auth.medical-certificate',compact('solutions'));
})->name('certificate');

Route::get('specialist-referral-home', function () {
    return view('auth.specialist-referrals-home');
})->name('specialist-referral-home');

// routes/web.php
Route::get('/medical-certificate/{param}/{action}', function ($param, $action) {
    if (Auth::check()) {
        $user = Auth::user();
        return view('auth.'.$action, ['param' => $param,'user'=>$user]);
    } else {
        session()->put('action', $action);
        session()->put('param', $param);

        return view('auth.not-registered-or-login', ['param' => $param,'action'=>$action]);
    }
})->name('medical-certificate');


Route::get('/telehealth-consultation/{param}', function ($param) {
    $title = Str::of($param)->explode('~~');


    session()->put('action', 'telehealth-request');
    session()->put('param',  $title[1]);
    session()->put('tele-consult-number', $title[0]);

    if (Auth::check()) {
        $user = Auth::user();
        return view('auth.telehealth-request', ['param' =>  $title[1],'user'=>$user]);
    } else {

        return view('auth.not-registered-or-login', ['param' =>  $title[1],'action'=>'telehealth-request']);
    }
})->name('telehealth-consultation');

Route::get('/consult-category', function () {

 

    $solutions  = Solutions::select('solutions.*')
    ->whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
              ->from('solutions')
              ->groupBy('solution_id');
    })
    ->where('category', 'Telehealth Consult')  // Add this condition to filter
    ->get();

        // Pass the solutions data to the view
        $message = "";
        return view('auth.doctor-consult-category', compact('solutions', 'message'));

})->name('consult-category');

Route::get('/telehealth', function () {
    return view('auth.doctor-consultation');
})->name('telehealth');


Route::get('/registered-patient', function () {
    return view('/');
})->name('registered-patient');

Route::get('/specialist-referrals', function () {
    return view('auth.specialist-referral');
});





Route::post('/login/{param}/{action}', [LoginController::class, 'login'])->name('login');
Route::get('/login/{param}/{action}', [LoginController::class, 'loginForm'])->name('loginForm');

Route::get('/register/{param}/{action}', [RegisterController::class,'showRegistrationForm'])->name('showRegistrationForm');
Route::post('/register/{param}/{action}', [RegisterController::class,'register'])->name('register');

Route::get('/change-password/{email}/{token}', [ForgottenPasswordController::class,'changePassword'])->name('change-password');
Route::post('/change-forggotten-password', [ForgottenPasswordController::class,'saveChangedPassword'])->name('change-forggotten-password');


Route::get('/verify-email/{email}/{token}', [VerifyEmailController::class,'send'])->name('send-verify-email');

Route::get('/forgotten-password', [ForgottenPasswordController::class,'send'])->name('forgotten-password');
 
Route::get('/auth/microsoft/redirect', [AuthMicrosoftLoginController::class, 'redirect'])->name('social-login');
Route::get('/auth/microsoft/callback', [AuthMicrosoftLoginController::class, 'callback'])->name('auth.microsoft.callback');

Route::get('/auth/google/redirect', [AuthGoogleLoginController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthGoogleLoginController::class, 'callback'])->name('auth.google.callback');
Route::get('/specialist-refferrals-payment', function () {
    return view('auth.specialist-referrals-home');
})->name('specialist-refferrals-payment');
Route::middleware(['auth'])->group(function () {

    Route::post('/create-mc-travelAndHoliday-payment-intent', [CertificateTravelAndHolidayController::class, 'getSecretKey'])->name('create-mc-travelAndHoliday-payment-intent');
    Route::post('/submit-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'storeMCDetails'])->name('submit-travelAndHoliday-medical-certificate');

    Route::post('/create-mc-care-payment-intent', [CertificateCareController::class,'getSecretKey'])->name('create-mc-care-payment-intent');
    Route::post('/validate-medicalDetails-care-medical-certificate', [CertificateCareController::class, 'medicalDetails'])->name('validate-medicalDetails-care-medical-certificate');
    Route::post('/submit-carer-medical-certificate', [CertificateCareController::class, 'storeMCDetails'])->name('submit-carer-medical-certificate');

    Route::post('/validate-personalDetails-care-medical-certificate', [CertificateCareController::class, 'personalDetails'])->name('validate-personalDetails-care-medical-certificate');
    Route::post('/validate-carerDetails-carer-medical-certificate', [CertificateCareController::class, 'carerDetails'])->name('validate-carerDetails-carer-medical-certificate');


    Route::post('/validate-personalDetails-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'personalDetails'])->name('validate-personalDetails-travelAndHoliday-medical-certificate');
    Route::post('/validate-medicalDetails-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'medicalDetails'])->name('validate-medicalDetails-travelAndHoliday-medical-certificate');

   
    Route::post('/validate-studiesDetails-studies-medical-certificate', [CertificateStudiesController::class, 'studiesDetails'])->name('validate-studiesDetails-studies-medical-certificate');
    Route::post('/validate-personalDetails-studies-medical-certificate', [CertificateStudiesController::class, 'personalDetails'])->name('validate-personalDetails-studies-medical-certificate');
    Route::post('/validate-medicalDetails-studies-medical-certificate', [CertificateStudiesController::class, 'medicalDetails'])->name('validate-medicalDetails-studies-medical-certificate');
    Route::post('/ submit-studies-medical-certificate', [CertificateStudiesController::class, 'storeMCDetails'])->name('submit-studies-medical-certificate');
    Route::post('/create-mc-studies-payment-intent', [CertificateStudiesController::class, 'getSecretKey'])->name('create-mc-studies-payment-intent');

    Route::post('/validate-personalDetails-work-medical-certificate', [CertificateWorkController::class, 'personalDetails'])->name('validate-personalDetails-work-medical-certificate');
    Route::post('/validate-medicalDetails-work-medical-certificate', [CertificateWorkController::class, 'medicalDetails'])->name('validate-medicalDetails-work-medical-certificate');
    Route::post('/validate-workDetails-work-medical-certificate', [CertificateWorkController::class, 'workDetails'])->name('validate-workDetails-work-medical-certificate');
    Route::post('/submit-work-medical-certificate', [CertificateWorkController::class, 'storeMCDetails'])->name('submit-work-medical-certificate');
    Route::post('/create-mc-work-payment-intent', [CertificateWorkController::class, 'getSecretKey'])->name('create-mc-work-payment-intent');

});

Route::middleware(['auth'])->group(function () {
    
    Route::post('/create-specialist-refferals-payment-intent', [SpecialistReferralsController::class,'getSecretKey'])->name('create-specialist-refferals-payment-intent');
    Route::post('/ save-specialist-refferals-details', [SpecialistReferralsController::class,'saveConsultDetails'])->name('save-specialist-refferals-details');

    Route::post('/ save-tele-consult-details', [TelehealthController::class,'saveConsultDetails'])->name(' save-tele-consult-details');

    Route::post('/create-tele-consult-payment-intent', [TelehealthController::class,'getSecretKey'])->name('create-tele-consult-payment-intent');


    Route::post('/telehealth-consultation-details', [TelehealthController::class,'consultationDetails'])->name('telehealth-consultation-details');

    Route::post('/telehealth-personal-details', [TelehealthController::class,'personalDetails'])->name('telehealth-personal-details');

    Route::post('/special-refferals-consultation-details', [SpecialistReferralsController::class,'consultationDetails'])->name('special-refferals-consultation-details');
    Route::post('/specialist-referral-personal-details', [SpecialistReferralsController::class,'personalDetails'])->name('specialist-referral-personal-details');
    Route::get('/weight-loss-payment', function () {
        return view('auth.weight-lost-home');
    })->name('weight-loss-payment');
    Route::post('/save-weight-loss-details', [WeightLostController::class,'saveConsultDetails'])->name('save-weight-loss-details');
    Route::post('/create-weight-loss-payment-intent', [WeightLostController::class,'getSecretKey'])->name('create-weight-loss-payment-intent');

    Route::post('/weight-loss-personal-details', [WeightLostController::class,'personalDetails'])->name('weight-loss-personal-details');
    Route::post('/weight-loss-consultation-details', [WeightLostController::class,'consultationDetails'])->name('weight-loss-consultation-details');
    Route::post('/weight-loss-medical-details', [WeightLostController::class,'medicalDetails'])->name('weight-loss-medical-details');
    Route::post('/submit-carer-leave-certificate', [CertificateController::class, 'careMC'])->name('submit-carer-leave-certificate');
    Route::post('/submit-travel-and-holiday-medical-certificate', [CertificateController::class, 'travelAndHoliday'])->name('submit-travel-and-holiday-medical-certificate');
    Route::post('/submit-studies-medical-certificate', [CertificateController::class, 'studies'])->name('submit-studiies-medical-certificate');
    Route::post('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/payment/{id}/{action}/{failLink}', [PaymentController::class, 'show'])->name('payment');
  //  Route::post('/payment', [PaymentController::class, 'make'])->name('payment');
    Route::get('/dashboard',[DashboardController::class,'index'] )->name('user.account');
});

Route::get('/auth/show-file-drives', [AuthGoogleDriveController::class, 'showProvider'])->name('show-google-drive');
Route::get('/auth/google-drive/redirect', [AuthGoogleDriveController::class, 'googleRedirect'])->name('auth.google-drive.redirect');
Route::get('/auth/google-drive/callback', [AuthGoogleDriveController::class, 'googleCallback'])->name('auth.google-drive.callback');
Route::post('/google-drive-downloaded-files', [AuthGoogleDriveController::class, 'downloadGoogleDriveFiles'])->name('google.drive.downloaded.files');

Route::get('/auth/dropbox/redirect', [AuthGoogleDriveController::class, 'dropboxRedirect'])->name('auth.dropbox.redirect');
Route::get('/auth/dropbox/callback', [AuthGoogleDriveController::class, 'dropboxCallback'])->name('auth.dropbox.callback');
Route::post('/dropbox-downloaded-files', [AuthGoogleDriveController::class, 'downloadDropboxFiles']);





