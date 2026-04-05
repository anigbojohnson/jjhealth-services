<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"Pathology referrals request")
@section('content')

   <!-- resources/views/auth/register.blade.php -->

   @vite(['resources/js/app.js', 'resources/js/pathology-referrals.js'])

   @php
   $datas =  [
        "PR01" => ["Pathology - Results Review"],
        "PR02" => ["Full blood count (FBC)","iron studies","b12 calcium","liver function tests (LFT)","kidney function (UEC)"],
        "PR03" => ["FBC", "ELFTs", "HDL/LDL + Fasting glucose","Iron Studies","Vitamin D","Vitamin B12"],
        "PR04" => ["ANA","ENA","EBV Serology","DHEAS", "Early Morning Cortisol (blood)"],
        "PR05" => ["Oestradiol","Progesterone","FSH LH","Prolactin","Testosterone"],
        "PR06" => ["Testosterone","Calculated Free Testosterone","SHBG","DHEAS","FSH LH"],
        "PR07" => ["BHCG"],
        "PR08" => ["Chlamydia"],
        "PR09" => ["HIV"],
        "PR10" => ["Urine drug screen"],
        "PR11" => ["Urine drug screen"],
        "PR12" => ["Mouth and nose swab PCR test for Covid 19"],
        "PR13" => ["Deamidated Gliadin Peptide Antibodies", "Tissue Transglutaminase IgA","Total Serum IgA"],
        "PR14" => ["ELFTs","High Sensitive CRP","HDL/LDL","Apolipoproteins A & B Lipoprotein (a)","Glucose"],
        "PR15" => ["Glucose", "HBA1c","HDL","Lipids","Urine Microalbumin"]
    ];
   @endphp






<div style="background-color:#D3D3D3; padding-bottom:30px;padding-top:80px; "> 
<div class="container" style="background-color:white; padding-bottom:30px;padding-top:30px;box-shadow: 0 2px 8px rgba(0,0,0,0.05);border-radius: 8px;border:2px solid #F2F2F2; ">
        <div class="progress mb-2">
            <div 
                class="progress-bar bg-info" 
                role="progressbar" 
                style="width: 33.33%" 
                aria-valuenow="33" 
                aria-valuemin="0" 
                aria-valuemax="100">
            </div>
    </div>
    <!-- Step Text -->
    <div class="step-text text-center">Step 1 of 4</div>



    <div id="pesonalDetails">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="text-center"  style="font-weight: 600;">Request {{ session('credentials')->solution_name }}</h3>
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
    
            <form id="personal-detail-form" method="POST" class="form-container">
                @csrf
    
                <div class="row mt-4 gy-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="fname" class="form-label fw-semibold">First Name</label>
                            <input id="fname" type="text" name="fname" value="{{ old('fname', Auth::user()->first_name) }}"  autocomplete="fname" autofocus class="form-control">
                            <span class="text-danger" id="fname-error"></span>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="lname" class="form-label">Last Name</label>
                            <input id="lname" type="text" name="lname" value="{{ old('lname', Auth::user()->last_name) }}"  autocomplete="lname" autofocus class="form-control">
                            <span class="text-danger" id="lname-error"></span>
                        
                        </div>
                    </div>
                </div>
    
                <div class="row gy-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="dob" class="form-label">Date Of Birth</label>
                            <input id="dob" type="date" name="dob" value="{{ old('dob', Auth::user()->dob) }}" required autocomplete="dob" autofocus class="form-control">
                            <span class="text-danger" id="dob-error"></span>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="pnumber" class="form-label">Phone Number</label>
                            <input id="pnumber" type="number" name="pnumber" value="{{ old('pnumber', Auth::user()->phone_number) }}" required autocomplete="pnumber" autofocus class="form-control">
                            <span class="text-danger" id="pnumber-error"></span>

                        </div>
                    </div>
                </div>
    
                <div class="row gy-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="gender" class="form-label">Gender</label>
                            <select class="form-select genderSelector" name="gender" id="gender" value="{{ old('gender', Auth::user()->gender) }}" required>
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
                            <select class="form-select genderSelector" name="indigene" id="indigene" value="{{ old('indigene', Auth::user()->indigene) }}" required>
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
                    <input id="address" type="text" name="address" value="{{ old('address', Auth::user()->address) }}" autocomplete="address" class="form-control">
                    <span class="text-danger" id="address-error"></span>

                </div>
    
                <div class="row gy-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" id="validate-button"  class="btn btn-dark btn-block w-100">Continue</button>
                        </div>
                    </div>
                </div>
            </form>
    
            </div>
        </div>
    </div>
    <div>

    <div id="consultationRequest">
        <div class="row justify-content-center">
            <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">Request {{ session('credentials')->solution_name }}</h3>


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
            
            <form id="consultation-pathology-refferals-form" method="POST" class="form-container mt-5" enctype="multipart/form-data">
                @csrf

                <hr>                
                <div class="">
                    @foreach($datas[session('credentials')->solution_id] as $item)
                        <div>
                            <input class="form-check-input"  type="checkbox" checked name="selected_tests[]" value="{{ $item }}" >
                            <label class="form-check-label">{{ $item }}</label>
                        </div>
                        <hr>
                    @endforeach
                    <span class="text-danger" id="pathology-listed-error"></span>
                </div>

                <div class="form-group mt-4">
                    <label class="mb-2" >Would you like to upload a photo of your condition?</label>
                    <div>
                        <input type="hidden" id="medicalConditionImage" name="medicalConditionImage" value="">
                        <div class="mb-2">
                            <button type="button" id="medicalConditionImageYes" class="btn btn-outline-primary option-btn" data-target="medicalConditionImage" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                            <button type="button" id="medicalConditionImageNo" class="btn btn-outline-primary option-btn" data-target="medicalConditionImage" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                        </div>
                        <div class="text-danger" id="medicalConditionImage-error"></div>
                    </div>
                </div>
                <!-- Hidden File Upload initially -->
                <div class="form-group mt-4" id="fileUploadGroup" style="display: none;">               
                     <label for="fileUpload" id="fileUploadButton" style="cursor: pointer;">Upload Image</label>
                    <!-- Hidden file input -->
                    <input type="file" class="form-control d-none" id="fileUpload" name="fileUpload">
                    <span id="file-name" class="text-muted mt-2"></span> <!-- Displays selected file name -->
                    <div class="text-danger" id="fileUpload-error"></div>

                </div>


                <div class="row mt-4 gy-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="button" id="back-personalDetails" class="btn btn-light btn-block rounded border border-grey w-100">Back</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" id="consult" class="btn btn-dark btn-block w-100">Continue</button>
                        </div>
                    </div>
                </div>
        
            </form>
    
            </div>
        </div>
    </div>
   
</div>

<div id="paymentRequest">
    <div class="row justify-content-center gy-3">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;"></h3>

            <h5>Payment Information</h5>
            <hr>

            <i> {{ session('credentials')->description }}</i> </br>


            <div class="row fw-bold">
                <div class="col-md-12">
                    <p>{{ session('credentials')->solution_name }}</p>
                    <p style="text-align: center;">${{ session('credentials')->cost }}</p>
                </div>

            </div>
            @if(session()->has('error'))
                <div class="alert alert-danger"> {{-- Change 'alert-success' to 'alert-danger' --}}
                    {{ session()->get('error') }}
                </div>
            @endif 
        
        <form id="payment-form" method="POST" class="form-container mt-4">
            @csrf

            <div class="card-icons mb-4">
                <img src="{{ asset('images/discover-icon.png') }}" alt="discover" class="card-icon">
                <img src="{{ asset('images/visa-icon.png') }}" alt="Visa" class="card-icon">
                <img src="{{ asset('images/mastercard-icon.png') }}" alt="MasterCard" class="card-icon">
                <img src="{{ asset('images/amex-icon.png') }}" alt="American Express" class="card-icon">
            </div>
          
            <div class="form-group">
                <label for="card-number">Card Number</label>
                <div id="card-number" class="StripeElement"  class="form-control"></div>
                <span class="text-danger" id="card-number-error"></span>

             </div>

            <div class="form-group">
                 <label for="card-expiry">Expiration Date</label>
                 <div id="card-expiry" class="StripeElement"  class="form-control"></div>
                 <span class="text-danger" id="card-expiry-error"></span>

           </div>  

             <div class="form-group">
                 <label for="card-cvc">CVC</label>
                  <div id="card-cvc" class="StripeElement"  class="form-control"></div>
                  <span class="text-danger" id="card-cvc-error"></span>

             </div>
            <div class="row mt-5">
          
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" id="validate-payment" class="btn btn-dark btn-block w-100">Pay</button>
                    </div>
                </div>
            </div>
        </form>

        </div>
    </div>
</div>
</div>


<style>
</style>
@endsection



