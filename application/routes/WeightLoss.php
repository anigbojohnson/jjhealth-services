<?php

use App\Http\Controllers\WeightLoss\WeightLostController;

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


Route::get('/weight-loss-consultation/{param}/{action}', function ($param, $action) {
    if (Auth::check()) {
        $user = Auth::user();
        return view('weightloss.'.$action, ['param' => $param,'user'=>$user]);
    } else {
        session()->put('action', $action);
        session()->put('param', $param);

        return view('auth.not-registered-or-login', ['param' => $param,'action'=>$action]);
    }
})->name('weight-loss-consultation');


Route::get('/weight-loss', function () {
    return view('weightloss.weight-lost-home');
})->name('weight-loss');