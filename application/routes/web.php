<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ForgottenPasswordController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\MedicalCertificate\CertificateController;
use App\Http\Controllers\WeightLoss\PaymentController;
use App\Http\Controllers\Auth\AuthMicrosoftLoginController;
use App\Http\Controllers\Auth\AuthGoogleLoginController;

use App\Http\Controllers\Auth\AuthMicrosoftRegisterController;
use App\Http\Controllers\Auth\AuthGoogleRegisterController;
use App\Http\Controllers\Auth\MedicationController;
use App\Http\Controllers\Auth\AuthGoogleDriveController;

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


Route::get('/', function () {
    return view('auth.home');
})->name('/');

Route::get('/faq', function () {
    return view('auth.faq');
})->name('feq');

Route::get('/registered-patient', function () {
    return view('/');
})->name('registered-patient');


Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'loginForm'])->name('loginForm');

Route::get('/register', [RegisterController::class,'showRegistrationForm'])->name('showRegistrationForm');
Route::post('/register', [RegisterController::class,'register'])->name('register');

Route::get('/change-password/{email}/{token}', [ForgottenPasswordController::class,'changePassword'])->name('change-password');
Route::post('/change-forggotten-password', [ForgottenPasswordController::class,'saveChangedPassword'])->name('change-forggotten-password');


Route::get('/verify-email/{email}/{token}', [VerifyEmailController::class,'send'])->name('send-verify-email');

Route::get('/forgotten-password', [ForgottenPasswordController::class,'send'])->name('forgotten-password');
 
Route::get('/auth/login/microsoft/redirect', [AuthMicrosoftLoginController::class, 'redirect'])->name('auth.login.microsoft.callback');
Route::get('/auth/login/microsoft/callback', [AuthMicrosoftLoginController::class, 'callback'])->name('auth.login.microsoft.callback');
Route::get('/auth/register/microsoft/redirect', [AuthMicrosoftRegisterController::class, 'redirect'])->name('auth.register.microsoft.redirect');
Route::get('/auth/register/microsoft/callback', [AuthMicrosoftRegisterController::class, 'callback'])->name('auth.register.microsoft.callback');

Route::get('/auth/login/google/redirect', [AuthGoogleLoginController::class, 'redirect'])->name('auth.login.google.redirect');
Route::get('/auth/login/google/callback', [AuthGoogleLoginController::class, 'callback'])->name('auth.login.google.callback');
Route::get('/auth/registr/google/redirect', [AuthGoogleRegisterController::class, 'redirect'])->name('auth.register.google.redirect');
Route::get('/auth/register/google/callback', [AuthGoogleRegisterController::class, 'callback'])->name('auth.register.google.callback');

Route::get('/specialist-refferrals-payment', function () {
    return view('auth.specialist-referrals-home');
})->name('specialist-refferrals-payment');


Route::middleware(['auth'])->group(function () {
    
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





require __DIR__.'/referrals.php';
require __DIR__.'/medical_certificates.php';
require __DIR__.'/treatment.php';
require __DIR__.'/WeightLoss.php';







