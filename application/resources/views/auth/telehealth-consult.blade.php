<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"Telehealth Consultation")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->
@vite(['resources/js/app.js', 'resources/js/medical-certificate-travel-and-holiday.js'])

<div class="container">
<div id="pesonalDetails">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">{{$param}}</h3>
            <hr>

            <h5>Verify Pesonal Details</h5>

            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif 
        
        @if(session()->has('error'))
            <div class="alert alert-danger"> {{-- Change 'alert-success' to 'alert-danger' --}}
                {{ session()->get('error') }}
            </div>
        @endif 
        
        <form id="register-form" method="POST" class="form-container">
            @csrf

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="fname" class="form-label fw-semibold">First Name</label>
                        <input id="fname" type="text" name="fname" value="{{ old('fname', $user->first_name) }}" required autocomplete="fname" autofocus class="form-control">
                        <span class="text-danger" id="fname-error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="lname" class="form-label">Last Name</label>
                        <input id="lname" type="text" name="lname" value="{{ old('lname', $user->last_name) }}" required autocomplete="lname" autofocus class="form-control">
                        <span class="text-danger" id="lname-error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="dob" class="form-label">Date Of Birth</label>
                        <input id="dob" type="date" name="dob" value="{{ old('dob', $user->dob) }}" required autocomplete="dob" autofocus class="form-control">
                        <span class="text-danger" id="dob-error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="pnumber" class="form-label">Phone Number</label>
                        <input id="pnumber" type="number" name="pnumber" value="{{ old('pnumber', $user->phone_number) }}" required autocomplete="pnumber" autofocus class="form-control">
                        <span class="text-danger" id="pnumber-error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="gender" class="form-label">Gender</label>
                        <select class="form-select genderSelector" name="gender" id="gender" value="{{ old('gender', $user->gender) }}" required>
                            <option value="not say" selected>Prefer not to say</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <span class="text-danger" id="gender-error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="indigene" class="form-label">Indigenous origin?</label>
                        <select class="form-select genderSelector" name="indigene" id="indigene" value="{{ old('indigene', $user->indigene) }}" required>
                            <option value="not say" selected>Prefer not to say</option>
                            <option value="no">No</option>
                            <option value="Aboriginal">Yes Aboriginal</option>
                            <option value="Torres Strait Islander origin">Yes Torres Strait Islander origin</option>
                        </select>
                        <span class="text-danger" id="indigene-error"></span>
                    </div>
                </div>
            </div>

            <div class="form-group mb-5">
                <label style="font-weight: 600;" for="address" class="form-label">Address</label>
                <input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" required autocomplete="address" class="form-control">
                <span class="text-danger" id="address-error"></span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="back" class="btn btn-light btn-block rounded border border-grey">Back</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="validate-button" class="btn btn-dark btn-block">Continue</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
  </div>
  <div id="medicalDetails">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">{{$param}}</h3>
            <hr>

            <h5>Your unforeseen illness or injury</h5>

            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif 
        
        @if(session()->has('error'))
            <div class="alert alert-danger"> {{-- Change 'alert-success' to 'alert-danger' --}}
                {{ session()->get('error') }}
            </div>
        @endif 
        
        <form id="register-form" method="POST" class="form-container">
            @csrf 
            <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label style="font-weight: 600;" for="detailedSymptoms" class="form-label">Please describe the timeline and the details of your symptoms</label>
                    <textarea id="detailedSymptoms" name="detailedSymptoms" required autocomplete="detailedSymptoms" autofocus class="form-control"></textarea>
                    <p style="font-size: 10px;">20 words minimum. What you type in here won't be added onto the letter.</p>
                    <span class="text-danger" id="detailedSymptoms-error"></span>
                </div>
            </div>
        </div>
        <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="back-work" class="btn btn-light btn-block rounded border border-grey">Back</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="validate-medical" class="btn btn-dark btn-block">Continue</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
</div>
@endsection