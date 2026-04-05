<?php
use App\Http\Controllers\Telehealth\TelehealthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Solutions;
use Illuminate\Http\Request;
use App\Models\CacheInvalidation;
use Illuminate\Support\Facades\Cache;


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


Route::post('/telehealth-consultation', function (Request $request) {
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
            'redirect_url' => route('telehealth-request') // a GET route
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'redirect_url' => route('not-registered-or-login')
        ]);
    }

})->name('telehealth-consultation');



Route::get('/telehealth-request', function () {
     return view('treatment.telehealth-request');
})->name('telehealth-request');




Route::middleware(['auth'])->group(function () {
    
    Route::post('/ save-tele-consult-details', [TelehealthController::class,'saveConsultDetails'])->name(' save-tele-consult-details');

    Route::post('/create-tele-consult-payment-intent', [TelehealthController::class,'getSecretKey'])->name('create-tele-consult-payment-intent');


    Route::post('/telehealth-consultation-details', [TelehealthController::class,'consultationDetails'])->name('telehealth-consultation-details');

    Route::post('/telehealth-personal-details', [TelehealthController::class,'personalDetails'])->name('telehealth-personal-details');
});



Route::get('/consult-category', function () {

    if (CacheInvalidation::wasInvalidated('teleconsult_solutions_1')) {
        Cache::forget('teleconsult_solutions_1');
        CacheInvalidation::clearFlag('teleconsult_solutions_1');
    }


    $solutions = Cache::rememberForever('teleconsult_solutions_1', function () {
        return Solutions::select('solutions.*')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                      ->from('solutions')
                      ->groupBy('solution_id');
            })
            ->where('category_id', 1)
            ->get();
    });

        // Pass the solutions data to the view
    $message = "";
    return view('treatment.doctor-consult-category', compact('solutions', 'message'));

})->name('consult-category');

Route::get('/telehealth', function () {
    return view('treatment.doctor-consultation');
})->name('telehealth');