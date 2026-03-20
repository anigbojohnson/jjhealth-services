<?php
use App\Http\Controllers\Telehealth\TelehealthController;
use Illuminate\Support\Facades\Route;


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
    return view('treatment.doctor-consult-category', compact('solutions', 'message'));
    
})->name('search-solutions');



Route::get('/telehealth-consultation/{param}', function ($param) {
    $title = Str::of($param)->explode('~~');


    session()->put('action', 'telehealth-request');
    session()->put('param',  $title[1]);
    session()->put('tele-consult-number', $title[0]);

    if (Auth::check()) {
        $user = Auth::user();
        return view('treatment.telehealth-request', ['param' =>  $title[1],'user'=>$user]);
    } else {

        return view('auth.not-registered-or-login', ['param' =>  $title[1],'action'=>'telehealth-request']);
    }
})->name('telehealth-consultation');





Route::middleware(['auth'])->group(function () {
    
    Route::post('/ save-tele-consult-details', [TelehealthController::class,'saveConsultDetails'])->name(' save-tele-consult-details');

    Route::post('/create-tele-consult-payment-intent', [TelehealthController::class,'getSecretKey'])->name('create-tele-consult-payment-intent');


    Route::post('/telehealth-consultation-details', [TelehealthController::class,'consultationDetails'])->name('telehealth-consultation-details');

    Route::post('/telehealth-personal-details', [TelehealthController::class,'personalDetails'])->name('telehealth-personal-details');
});



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
        return view('treatment.doctor-consult-category', compact('solutions', 'message'));

})->name('consult-category');

Route::get('/telehealth', function () {
    return view('treatment.doctor-consultation');
})->name('telehealth');