<?php

use App\Http\Controllers\Pathology\PathologyReferralsController;
use App\Models\Category;
use App\Models\Solutions;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CacheInvalidation;
use Illuminate\Support\Facades\Cache;


Route::get('/pathology', function () {
    return view('pathology.pathology-home');
})->name('pathology');


Route::post('/pathology-referrals/request', function (Request $request) {

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
        return response()->json([
            'status' => 'success',
            'redirect_url' => route('pathology-referral-application') 
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'redirect_url' => route('not-registered-or-login')
        ]);
    }
})->name('pathology-referrals.request');


Route::get('/pathology-referral-application', function () {
     return view('pathology.pathology-request');
})->name('pathology-referral-application');


Route::get('/pathology/select', function () {
    dd(Cache::has('pathology_solutions_6'));

    if (CacheInvalidation::wasInvalidated('pathology_solutions_6')) {
        Cache::forget('pathology_solutions_6');
        CacheInvalidation::clearFlag('pathology_solutions_6');
    }


    $solutions = Cache::rememberForever('pathology_solutions_6', function () {
        return Solutions::select('solutions.*')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('solutions')
                      ->groupBy('solution_id');

                
            })
            ->where('category_id', 6)
            ->get();
    });


    return view('pathology.pathology-choice',compact('solutions'));
})->name('pathology.select');


Route::middleware(['auth'])->group(function () {
    
    Route::post('/create-pathology-refferals-payment-intent', [PathologyReferralsController::class,'getSecretKey'])->name('create-specialist-refferals-payment-intent');
    Route::post('/save-pathology-refferals-details', [PathologyReferralsController::class,'saveConsultDetails'])->name('save-specialist-refferals-details');

    Route::post('/pathology-refferals-consultation-details', [PathologyReferralsController::class,'consultationDetails'])->name('special-refferals-consultation-details');
    Route::post('/pathology-referral-personal-details', [PathologyReferralsController::class,'personalDetails'])->name('specialist-referral-personal-details');
});
