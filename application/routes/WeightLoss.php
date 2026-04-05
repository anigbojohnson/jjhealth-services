<?php

use App\Http\Controllers\WeightLoss\WeightLostController;

use App\Models\Category;
use App\Models\Solutions;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


Route::middleware(['auth'])->group(function () {

    Route::get('/weight-loss-payment', function () {
        return view('weightloss.weight-lost-home');
    })->name('weight-loss-payment');

    Route::post('/save-weight-loss-details', [WeightLostController::class,'saveConsultDetails'])->name('save-weight-loss-details');
    Route::post('/create-weight-loss-payment-intent', [WeightLostController::class,'getSecretKey'])->name('create-weight-loss-payment-intent');
    Route::post('/weight-loss-personal-details', [WeightLostController::class,'personalDetails'])->name('weight-loss-personal-details');
    Route::post('/weight-loss-consultation-details', [WeightLostController::class,'consultationDetails'])->name('weight-loss-consultation-details');
    Route::post('/weight-loss-medical-details', [WeightLostController::class,'medicalDetails'])->name('weight-loss-medical-details');
});


Route::post('/weight-loss-consultation', function (Request $request) {

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
        return view('weightloss.weight-loss-request');
    } else {

        return view('auth.not-registered-or-login');
    }
})->name('weight-loss-consultation');


Route::get('/weight-loss', function () {
    if (CacheInvalidation::wasInvalidated('weightloss_solutions_5')) {
        Cache::forget('weightloss_solutions_5');
        CacheInvalidation::clearFlag('weightloss_solutions_5');
    }


    $solutions = Cache::rememberForever('weightloss_solutions_5', function () {
        return Solutions::select('solutions.*')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('solutions')
                      ->groupBy('solution_id');
            })
            ->where('category_id', 5)
            ->get();
    });

    return view('weightloss.weight-lost-home',compact('solutions'));
})->name('weight-loss');