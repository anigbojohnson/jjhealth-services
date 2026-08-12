<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;



class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_password(Request $request)
{
    $validatedData = $request->validate([
        'current_password' => [
        'required',
        'string',
        ],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
           'different:current_password',

        ],
    ], [
        'current_password.required' => 'Please enter your current password.',
        'password.different' => 'Your new password must be different from your current password.',
        'password.required' => 'Please enter a new password.',
        'password.min' => 'Your new password must be at least 8 characters.',
        'password.confirmed' => 'The password confirmation does not match.',
    ]);

    $user = Auth::user();

    // Verify the current password
    if (!Hash::check($validatedData['current_password'], $user->password)) {
        return response()->json([
            'errors' => [
                'current_password' => [
                    'The current password is incorrect.'
                ]
            ]
        ], 422);
    }

    // Update password
    $user->update([
        'password' => Hash::make($validatedData['password']),
    ]);

    return response()->json([
        'message' => 'Password changed successfully.',
        'redirect' => route('view-profile'),
    ], 200);


}


    public function show_change_password()
    {
        return view('dashboard.change-password');
    }
    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request){    

    $validatedData = $request->validate([
    'first_name' => 'required|string',
    'last_name' => 'required|string',
    'phone_number' => [
        'required',
        'regex:/^(?:\+61|0)[2-478](?:[ -]?[0-9]){8}$/'
    ],
    'dob' => 'required|date|before:-18 years',
    'gender' => 'required|in:male,female,not say',
    'indigene' => 'required|in:,not say,no,Aboriginal,Torres Strait Islander origin',
    'address' => 'required|string',
    'profile_picture' => [
    'nullable',
    'image',
    'mimes:jpg,jpeg,png,webp',
    'max:2048',
    ],
    ], 
    [
    'fname.required' => 'Please enter your first name.',
    'fname.string' => 'First name must be a valid name.',

    'lname.required' => 'Please enter your last name.',
    'lname.string' => 'Last name must be a valid name.',

    'pnumber.required' => 'Please enter your phone number.',
    'pnumber.regex' => 'Please enter a valid Australian phone number.',

    'dob.required' => 'Please enter your date of birth.',
    'dob.date' => 'Please enter a valid date of birth.',
    'dob.before' => 'You must be at least 18 years old.',

    'gender.required' => 'Please select your gender.',
    'gender.in' => 'Please select a valid gender option.',

    'indigene.required' => 'Please select an option.',
    'indigene.in' => 'Please select a valid option.',

    'address.required' => 'Please enter your address.',
    'address.string' => 'Please enter a valid address.',

    'profile_picture.image' => 'The profile picture must be a valid image.',
    'profile_picture.mimes' => 'The profile picture must be a JPG, JPEG, PNG, or WEBP image.',
    'profile_picture.max' => 'The profile picture must not be larger than 2 MB.',
    
]);

    $user = Auth::user();

    $profilePicture = $user->profile_picture;
    $file = $request->file('profile_picture');

    if ($request->hasFile('profile_picture')) {

        // Remove previous picture
        if (
            $user->profile_picture
        ) {
            Storage::disk('s3')->delete($user->profile_picture);

            $fileName = time() . '_' . $file->getClientOriginalName();

            Storage::disk('s3')->putFileAs(
                'profile-pictures/' . Auth::id(),
                $file,
                $fileName,
                'public'
            );
        }

        // Upload new picture
        $profilePicture = $request->file('profile_picture')->store(
            'profile-pictures/' . $user->id,
            's3'
        );
    }

    $user->update([
        'first_name'    => $validatedData['first_name'],
        'last_name'     => $validatedData['last_name'],
        'phone'         => $validatedData['phone_number'],
        'date_of_birth' => $validatedData['dob'],
        'gender'        => $validatedData['gender'],
        'indigene'      => $validatedData['indigene'],
        'address'       => $validatedData['address'],
        'profile_picture' => $profilePicture,
    ]);

    return response()->json([
    'message' => 'Profile updated successfully',
    'redirect' => route('view-profile')
]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
}
