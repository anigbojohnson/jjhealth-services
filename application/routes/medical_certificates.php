<?php


use App\Http\Controllers\MedicalCertificate\CertificateWorkController;
use App\Http\Controllers\MedicalCertificate\CertificateStudiesController;
use App\Http\Controllers\MedicalCertificate\CertificateTravelAndHolidayController;
use App\Http\Controllers\MedicalCertificate\CertificateCareController;
use App\Models\Solutions;
use Illuminate\Http\Request;

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
    ->whereIn('category_id', [7, 8, 9, 10])  // Add this condition to filter
    ->get();

    return view('medical-certificate.medical-certificate',compact('solutions'));
})->name('certificate');



Route::post('/medical-certificate/request', function (Request $request) {
    $credentials = new \stdClass();
    $id = $request->input('id'); 
    $solution_id = $request->input('solution_id'); 
    $solution_name = $request->input('solution_name'); 
    $description = $request->input('description');
    $cost = $request->input('cost');


    $credentials->id =  $id;
    $credentials->solution_id =  strtoupper($solution_id);
    $credentials->solution_name =  $solution_name;
    $credentials->description =  $description;
    $credentials->cost =  $cost;
    $credentials->days_number = in_array($request->input('solution_id'), ['MC01','MC02','MC03','MC04']) ? 'single' : 'multiple';
    session()->put('credentials', $credentials);

    if (Auth::check()) {
        if(in_array(session('credentials')->solution_id , ['MC01','MC05']) )
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('medical-certificate.work') 
            ]);
        
        if(in_array(session('credentials')->solution_id , ['MC02','MC06']))
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('medical-certificate.studies') 
            ]);
        if(in_array(session('credentials')->solution_id , ['MC03','MC07']))
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('medical-certificate.carer') 
            ]);
        if(in_array(session('credentials')->solution_id , ['MC04','MC08']))
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('medical-certificate.travel-and-holiday') 
            ]);
    } else {
        return response()->json([
            'status' => 'error',
            'redirect_url' => route('not-registered-or-login')
        ]);
    }
})->name('medical-certificate.request');



Route::get('/medical-certificate-application', function () {


})->name('medical-certificate-application');

Route::get('/medical-certificate-work', function () {
      return view('medical-certificate.work-medical-certificate');
})->name('medical-certificate.work');

Route::get('/medical-certificate-studies', function () {
      return view('medical-certificate.studies-medical-certificate');
})->name('medical-certificate.studies');

Route::get('/medical-certificate-carer', function () {
      return view('medical-certificate.carers-Leave-certificate');
})->name('medical-certificate.carer');

Route::get('/medical-certificate-travel-and-holiday', function () {
      return view('medical-certificate.travel-and-holiday-certificate');
})->name('medical-certificate.travel-and-holiday');