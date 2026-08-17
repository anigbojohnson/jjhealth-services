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
use App\Models\PathologyReferrals;
use App\Http\Controllers\Auth\AuthMicrosoftRegisterController;
use App\Http\Controllers\Auth\AuthGoogleRegisterController;
use App\Http\Controllers\Auth\MedicationController;
use App\Http\Controllers\Auth\AuthGoogleDriveController;

use App\Models\Solutions;
use App\Models\MedicalCertificate;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Referral;

Route::middleware(['throttle:api'])->group(function () {

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
        Route::post('/forgotten-password', [ForgottenPasswordController::class,'send'])->name('forgotten-password');
        
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

        Route::get('/auth/show-file-drives', [AuthGoogleDriveController::class, 'showProvider'])->name('show-google-drive');
        Route::get('/auth/google-drive/redirect', [AuthGoogleDriveController::class, 'googleRedirect'])->name('auth.google-drive.redirect');
        Route::get('/auth/google-drive/callback', [AuthGoogleDriveController::class, 'googleCallback'])->name('auth.google-drive.callback');
        Route::post('/google-drive-downloaded-files', [AuthGoogleDriveController::class, 'downloadGoogleDriveFiles'])->name('google.drive.downloaded.files');

        Route::get('/auth/dropbox/redirect', [AuthGoogleDriveController::class, 'dropboxRedirect'])->name('auth.dropbox.redirect');
        Route::get('/auth/dropbox/callback', [AuthGoogleDriveController::class, 'dropboxCallback'])->name('auth.dropbox.callback');
        Route::post('/dropbox-downloaded-files', [AuthGoogleDriveController::class, 'downloadDropboxFiles']);


        Route::get('/not-registered-or-login', function () {
            return view('auth.not-registered-or-login');
        })->name('not-registered-or-login');

});



Route::middleware(['auth','throttle:api'])->group(function () {
        Route::post('/password-update',[DashboardController::class,'edit_password'] )->name('password-update');
        Route::get('/change-password',[DashboardController::class,'show_change_password'] )->name('change-password');
        Route::get('/dashboard',[DashboardController::class,'index'] )->name('dashboard');
        Route::post('/edit-profile',[DashboardController::class,'update'] )->name('edit-profile');
        Route::get('/my-certificate', function () {

            $certificates = MedicalCertificate::select([
                    'id',
                    'requestDate',
                    'seeking',
                    'validFrom',
                    'validTo',
                    'request_status'
                ])
                ->where('user_email', Auth::user()->email)
                ->latest('requestDate')
                ->paginate(10);
                
                return view('dashboard.mycertificate', [
                    'certificates' => $certificates,
                ]);

        })->name('my-certificate');


        Route::get('/my-referrals', function () {
            $referrals = Referral::with('catagory')
                ->where('user_email', Auth::user()->email)
                ->latest()
                ->paginate(10);

            return view('dashboard.myreferrals', [
                'referrals' => $referrals,
            ]);

        })->name('my-referrals');

        Route::get('/view-profile', function () {

            return view('dashboard.view-profile');
        })->name('view-profile');

        Route::get('/edit-profile', function () {

            return view('dashboard.edit-profile');
        })->name('edit-profile');        
        
        Route::get('/my-result', function () {

            $pathologiesResults = PathologyReferrals::with('referral')
                            ->whereHas('referral', function ($query) {
                                $query->where('user_email', Auth::user()->email);
                            })
                            ->latest()
                            ->paginate(10);
            return view('dashboard.myresult', [
                    'pathologiesResults' =>  $pathologiesResults,
            ]);

        })->name('my-result');



        Route::get('/download/{$fileName}', function ($fileName) {
                // This must match the path used when uploading
                
                $email = Auth::user()->email;
                $filePath = 'user-temp-file/' . $email . '/' . $fileName;

                // Make sure the file exists in S3
                if (!Storage::disk('s3')->exists($filePath)) {
                    abort(404, 'Certificate file not found.');
                }

                return Storage::disk('s3')->download(
                    $filePath,
                    $fileName
                );

        })->name('download'); 
    });



Route::middleware(['throttle:api'])->group(function () {

    require __DIR__.'/referrals.php';
    require __DIR__.'/medical_certificates.php';
    require __DIR__.'/treatment.php';
    require __DIR__.'/WeightLoss.php';
    require __DIR__.'/pathology.php';

});





