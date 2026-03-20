<?php


use App\Http\Controllers\MedicalCertificate\CertificateWorkController;
use App\Http\Controllers\MedicalCertificate\CertificateStudiesController;
use App\Http\Controllers\MedicalCertificate\CertificateTravelAndHolidayController;
use App\Http\Controllers\MedicalCertificate\CertificateCareController;
use App\Models\Solutions;

Route::middleware(['auth'])->group(function () {

    Route::post('/validate-personalDetails-work-medical-certificate', [CertificateWorkController::class, 'personalDetails'])->name('validate-personalDetails-work-medical-certificate');
    Route::post('/validate-medicalDetails-work-medical-certificate', [CertificateWorkController::class, 'medicalDetails'])->name('validate-medicalDetails-work-medical-certificate');
    Route::post('/validate-workDetails-work-medical-certificate', [CertificateWorkController::class, 'workDetails'])->name('validate-workDetails-work-medical-certificate');
    Route::post('/submit-work-medical-certificate', [CertificateWorkController::class, 'storeMCDetails'])->name('submit-work-medical-certificate');
    Route::post('/create-mc-work-payment-intent', [CertificateWorkController::class, 'getSecretKey'])->name('create-mc-work-payment-intent');


    Route::post('/validate-studiesDetails-studies-medical-certificate', [CertificateStudiesController::class, 'studiesDetails'])->name('validate-studiesDetails-studies-medical-certificate');
    Route::post('/validate-personalDetails-studies-medical-certificate', [CertificateStudiesController::class, 'personalDetails'])->name('validate-personalDetails-studies-medical-certificate');
    Route::post('/validate-medicalDetails-studies-medical-certificate', [CertificateStudiesController::class, 'medicalDetails'])->name('validate-medicalDetails-studies-medical-certificate');
    Route::post('/ submit-studies-medical-certificate', [CertificateStudiesController::class, 'storeMCDetails'])->name('submit-studies-medical-certificate');
    Route::post('/create-mc-studies-payment-intent', [CertificateStudiesController::class, 'getSecretKey'])->name('create-mc-studies-payment-intent');


    Route::post('/create-mc-travelAndHoliday-payment-intent', [CertificateTravelAndHolidayController::class, 'getSecretKey'])->name('create-mc-travelAndHoliday-payment-intent');
    Route::post('/submit-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'storeMCDetails'])->name('submit-travelAndHoliday-medical-certificate');
    Route::post('/validate-personalDetails-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'personalDetails'])->name('validate-personalDetails-travelAndHoliday-medical-certificate');
    Route::post('/validate-medicalDetails-travelAndHoliday-medical-certificate', [CertificateTravelAndHolidayController::class, 'medicalDetails'])->name('validate-medicalDetails-travelAndHoliday-medical-certificate');


    Route::post('/create-mc-care-payment-intent', [CertificateCareController::class,'getSecretKey'])->name('create-mc-care-payment-intent');
    Route::post('/validate-medicalDetails-care-medical-certificate', [CertificateCareController::class, 'medicalDetails'])->name('validate-medicalDetails-care-medical-certificate');
    Route::post('/submit-carer-medical-certificate', [CertificateCareController::class, 'storeMCDetails'])->name('submit-carer-medical-certificate');
    Route::post('/validate-personalDetails-care-medical-certificate', [CertificateCareController::class, 'personalDetails'])->name('validate-personalDetails-care-medical-certificate');
    Route::post('/validate-carerDetails-carer-medical-certificate', [CertificateCareController::class, 'carerDetails'])->name('validate-carerDetails-carer-medical-certificate');

});



Route::get('/certificate', function () {


    $solutions  = Solutions::select('solutions.*')
    ->whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
              ->from('solutions')
              ->groupBy('solution_id');
    })
    ->where('category_id', 2)  // Add this condition to filter
    ->get();

    return view('medical-certificate.medical-certificate',compact('solutions'));
})->name('certificate');



Route::get('/medical-certificate/{param}/{action}', function ($param, $action) {
    if (Auth::check()) {
        $user = Auth::user();
        return view('medical-certificate.'.$action, ['param' => $param,'user'=>$user]);
    } else {
        session()->put('action', $action);
        session()->put('param', $param);

        return view('auth.not-registered-or-login', ['param' => $param,'action'=>$action]);
    }
})->name('medical-certificate');