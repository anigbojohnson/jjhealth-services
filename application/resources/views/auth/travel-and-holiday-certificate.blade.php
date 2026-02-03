<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"Travel and holiday medical certificate")
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
                        <button type="button" id="back-home" class="btn btn-light btn-block rounded border border-grey">Back</button>
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

    
        
        <form id="register-medical-and-holiday-cancellation-form" method="POST" class="form-container">
            @csrf
            <div class="form-group mt-4">
                <label for="preExistingHealth">Do you have any pre-existing health conditions your Partner Practitioner should be aware of?</label>
                <div>
                    <input type="hidden" id="preExistingHealthYes" name="preExistingHealth"  value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="preExistingHealth"  data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="preExistingHealth"  data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="preExistingHealth-error"></div>

                </div>
            </div>


            <div id="healthConditions">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                              <label for="informationPreExistingHealthYes">Please provide information about your pre-existing health conditions. </label><br>
                            <input id="informationPreExistingHealthYes" type="text" name="informationPreExistingHealthYes" value="{{ old('informationPreExistingHealthYes') }}"  autocomplete="informationPreExistingHealthYes" autofocus class="form-control">
                             <span class="text-danger " id="informationPreExistingHealthYes-error"></span>

                        </div>
                    </div>
                </div>    
            </div>
            
            <div class="form-group mt-4">
                <label for= "medicationsRegularly">Are you taking any medications regularly?</label>
                <div>
                    <input type="hidden" id="medicationsRegularlyYes" name="medicationsRegularly"  value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="medicationsRegularly" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="medicationsRegularly" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="medicationsRegularly-error"></div>

                </div>
            </div>
            <input type="hidden" id="treatment_category" name="treatment_category" value="{{ $param }}">

            
            <div id="medicationRegimen" >
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="medicationsRegularlyInfo">Please provide information about your standard medication regimen.</label><br>
                            <input id="medicationsRegularlyInfo" type="text" name="medicationsRegularlyInfo" value="{{ old('medicationsRegularlyInfo') }}"  autocomplete="medicationsRegularlyInfo" autofocus class="form-control">
                            <span class="text-danger " id="medicationsRegularlyInfo-error"></span>

                        </div>
                    </div>
                </div>
            </div>
    

            <div class="row">
         
                <div class="col-md-12">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="medicalLetterReasons" class="form-label">Main reason for medical letter</label>
                        <select class="form-select genderSelector" name="medicalLetterReasons" id="medicalLetterReasons" value="{{ old('medicalLetterReasons') }}" required>
                            <option value="noOption">please select an option</option>
                            <option value="Serious illness">Serious illness</option>
                            <option value="Serious illness">Acute injury</option>
                            <option value="Hospitalization or surgery">Hospitalization or surgery</option>
                            <option value="Flare-ups of chronic condition">Flare-ups of chronic condition</option>
                            <option value="Mental health crisis">Mental health crisis</option>
                            <option value="Destress due to bereavement">Destress due to bereavement</option>
                            <option value="Infectious Disease">Infectious Disease</option>
                            <option value="Pregnancy related complications">Pregnancy related complications</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="text-danger" id="medicalLetterReasons-error"></span>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label style="font-weight: 600;" for="startDateSymptoms" class="form-label">Start date of symptoms</label>
                        <input id="startDateSymptoms" type="date" name="startDateSymptoms" value="{{ old('startDateSymptoms') }}" required autocomplete="startDateSymptoms" autofocus class="form-control">
                        <span class="text-danger" id="startDateSymptoms-error"></span>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="form-group">
                    <label style="font-weight: 600;" for="detailedSymptoms" class="form-label">Please describe the timeline and the details of your symptoms</label>
                    <textarea rows="6" cols="50" style="100%"id="detailedSymptoms" name="detailedSymptoms" required autocomplete="detailedSymptoms" autofocus class="form-control"></textarea>
                    <p style="font-size: 10px;">20 words minimum. What you type in here won't be added onto the letter.</p>
                    <span class="text-danger" id="detailedSymptoms-error"></span>
                </div>
            </div>

            
    
        <div class="col-md-12">
            <div class="form-group">
                <label style="font-weight: 600;" for="privacy" class="form-label">Would you like your Partner Practitioner to include health details and symptoms in your letter, or prefer a more generic approach for privacy?</label>
                <select class="form-select genderSelector" name="privacy" id="privacy" value="{{ old('privacy') }}">
                    <option value="Yes Include specific health details and symptoms">Yes, Include specific health details and symptoms</option>
                    <option value="No maintain generic approach for confidentiality">No, maintain generic approach for confidentiality</option>
                </select>
                <span class="text-danger" id="privacy-error"></span>
            </div>
        </div>


    
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="back-personal" class="btn btn-light btn-block rounded border border-grey">Back</button>
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




<div id="previewDetails">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">{{$param}}</h3>
            <hr>

            <h5 id="reviewDetails">Review your details</h5>

    
      
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="back-medicals" class="btn btn-light btn-block rounded border border-grey">Back</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="submit-holidayAndTravelCouncillation-medical-certificate" class="btn btn-dark btn-block">Continue</button>
                    </div>
                </div>
            </div>
        

        </div>
    </div>
</div>


<div id="paymentRequest">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">Request Travel and holiday Certificate</h3>
            <hr>

            <h5>Payment Information</h5>

        
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
                        <button type="submit" id="validate-payment" class="btn btn-dark btn-block">Pay</button>
                    </div>
                </div>
            </div>
        </form>

        </div>
    </div>
</div>


</div>

@endsection