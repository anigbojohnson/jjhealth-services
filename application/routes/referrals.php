<?php

use App\Http\Controllers\Pathology\PathologyController;
use App\Models\Category;
use App\Models\Solutions;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;




Route::get('/specialist_referrals', function () {
    return view('referals.specialist-referrals-home');
})->name('specialist_referrals');


Route::get('/specialist-referrals', function () {
    return view('referals.specialist-referral');
});


Route::post('/specialist-referrals/request', function (Request $request) {
    $credentials = new \stdClass();
    $id = $request->input('id'); 
    $solution_id = $request->input('solution_id'); 
    $solution_name = $request->input('solution_name'); 
    $description = $request->input('description');
    $cost = $request->input('cost');


    $credentials->id =  $id;
    $credentials->solution_id =  $solution_id;
    $credentials->solution_name =  $solution_name;
    $credentials->description =  $description;
    $credentials->cost =  $cost;



    session()->put('credentials', $credentials);

   if (Auth::check()) {
        $user = Auth::user();
        return view('referals.specialist-referrals-request');
    } else {
        return view('auth.not-registered-or-login');
    }
})->name('specialist-referrals.request');

Route::get('/specialist-referral/select', function () {

   $categoryId = 4;
    $solutions = Category::find($categoryId)->solutions;
    return view('referals.specialist-referrals-choice',compact('solutions') );
})->name('referral.specialist-referral.select');

Route::get('/specialist-referral-home', function () {

    return view('referals.specialist-referrals-home');
})->name('specialist-referral-home');


Route::middleware(['auth'])->group(function () { 
    Route::post('/create-specialist-refferals-payment-intent', [SpecialistReferralsController::class,'getSecretKey'])->name('create-specialist-refferals-payment-intent');
    Route::post('/ save-specialist-refferals-details', [SpecialistReferralsController::class,'saveConsultDetails'])->name('save-specialist-refferals-details');
    Route::post('/special-refferals-consultation-details', [SpecialistReferralsController::class,'consultationDetails'])->name('special-refferals-consultation-details');
    Route::post('/specialist-referral-personal-details', [SpecialistReferralsController::class,'personalDetails'])->name('specialist-referral-personal-details');
});