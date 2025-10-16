<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"weight loss")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->

   @vite(['resources/js/app.js', 'resources/js/weight-loss.js'])

   
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
            
            <form id="personal-detail-form" action="{{ route('weight-loss-personal-details') }}" method="POST" class="form-container">
                @csrf
    
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="fname" class="form-label fw-semibold">First Name</label>
                            <input id="fname" type="text" name="fname" value="{{ old('fname', $user->first_name) }}"  autocomplete="fname" autofocus class="form-control">
                            <span class="text-danger" id="fname-error"></span>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-weight: 600;" for="lname" class="form-label">Last Name</label>
                            <input id="lname" type="text" name="lname" value="{{ old('lname', $user->last_name) }}"  autocomplete="lname" autofocus class="form-control">
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
                    <input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" autocomplete="address" class="form-control">
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
                            <button type="submit" id="validate-button" class="btn btn-dark btn-block">Continue</button>
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
                <h3 class="text-center"  style="font-weight: 600;"></h3>
    
                <h5>Consultation request details</h5>
                <hr>

    
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
            
            <form id="consultation-loss-form" method="POST" class="form-container mt-5">
                @csrf
                <div class="form-group">
                    <label> What is the reason for your request?</label>  
                    <input id="requestReason" type="text" name="requestReason" value=""  autocomplete="request-reason" autofocus class="form-control"> 
                    <span class="text-danger" id="requestReason-error"></span>

                </div>
         
    
                <div class="form-group mt-4">
                    <label>Please enter your height (in cm).</label>
                        <input type="number" style="width:100%; height:40px;" id ="height" name="height"  class="form-control">  
                        <span class="text-danger" id="height-error"></span>
                </div>
    
                <div class="form-group mt-4">
                    <label> Please enter your weight (in kg).</label>
                    <div>
                        <input type="number" style="width:100%; height:40px;" id="weight" name="weight"  class="form-control"> 
                        <span class="text-danger" id="weight-error"></span>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="button" id="back-personalDetails" class="btn btn-light btn-block rounded border border-grey">Back</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" id="consult" class="btn btn-dark btn-block">Continue</button>
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
            <h3 class="text-center"  style="font-weight: 600;"></h3>
         

            <h3>Medical Information</h3>
            <hr>

            <div class="md-5" style="color:red" id="text-invalid"></div>


        <form id="medical-detail-form" method="POST" action="{{ route('weight-loss-medical-details') }}" class="form-container">
            @csrf
            <div class="form-group">
                <label>Have you ever used medication for weight loss in the past?</label>
                <div>
                    <input type="hidden" id="medication_used" name="medication_used" value="">
                    <button type="button"  class="btn btn-outline-primary option-btn" data-target="medication_used" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="medication_used" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="medication_used-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Have you suffered from diseases with your pancreas, liver or kidneys?</label>
                <div>
                    <input type="hidden" id="diseases_pancreas_liver_kidneys" name="diseases_pancreas_liver_kidneys" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="diseases_pancreas_liver_kidneys" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="diseases_pancreas_liver_kidneys" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="diseases_pancreas_liver_kidneys-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Are you taking insulin?</label>
                <div>
                    <input type="hidden" id="taking_insulin" name="taking_insulin" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="taking_insulin" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="taking_insulin" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="taking_insulin-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Have you ever had an allergic reaction to any medication?</label>
                <div>
                    <input type="hidden" id="allergic_reaction" name="allergic_reaction" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="allergic_reaction" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="allergic_reaction" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="allergic_reaction-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Do you have any allergies (medication, food or any product)?</label>
                <div>
                    <input type="hidden" id="any_allergies" name="any_allergies" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="any_allergies" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="any_allergies" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="any_allergies-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Are you pregnant or are you actively trying to get pregnant now or in the next 6 months?</label>
                <div>
                    <input type="hidden" id="pregnant" name="pregnant" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="pregnant" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="pregnant" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="pregnant-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Have you ever been diagnosed with an eating disorder?</label>
                <div>
                    <input type="hidden" id="eating_disorder" name="eating_disorder" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="eating_disorder" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="eating_disorder" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="eating_disorder-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Do you suffer from cardiovascular disease (e.g. heart disease, heart attack or irregular heartbeat), untreated high blood pressure, peripheral vascular disease, or have had a stroke?</label>
                <div>
                    <input type="hidden" id="cardiovascular_disease" name="cardiovascular_disease" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="cardiovascular_disease" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="cardiovascular_disease" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="cardiovascular_disease-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Are you currently taking strong pain killers?</label>
                <div>
                    <input type="hidden" id="strong_pain_killers" name="strong_pain_killers" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="strong_pain_killers" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="strong_pain_killers" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="strong_pain_killers-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Have you ever been diagnosed with severe heart failure?</label>
                <div>
                    <input type="hidden" id="severe_heart_failure" name="severe_heart_failure" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="severe_heart_failure" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="severe_heart_failure" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="severe_heart_failure-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Do you have a known brain tumour?</label>
                <div>
                    <input type="hidden" id="brain_tumour" name="brain_tumour" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="brain_tumour" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="brain_tumour" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="brain_tumour-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Have you ever had bariatric surgery (e.g. gastric band or gastric bypass)?</label>
                <div>
                    <input type="hidden" id="bariatric_surgery" name="bariatric_surgery" value="">
                    <button type="button" id="bariatric_surgery-yes" class="btn btn-outline-primary option-btn" data-target="bariatric_surgery" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" id="bariatric_surgery-no" class="btn btn-outline-primary option-btn" data-target="bariatric_surgery" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="bariatric_surgery-error"></div>

                </div>
            </div>
            <div class="form-group mt-4">
                <label>Do you suffer from a condition called gastroparesis or delayed gastric emptying?</label>
                <div>
                    <input type="hidden" id="gastroparesis" name="gastroparesis" value="">
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="gastroparesis" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" class="btn btn-outline-primary option-btn" data-target="gastroparesis" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="gastroparesis-error"></div>

                </div>
            </div>

            <div class="form-group mt-4">
                <label>Would you like to upload a photo of your yourself?</label>
                <div>
                    <input type="hidden" id="medicalConditionImage" name="medicalConditionImage" value="">
                    <button type="button" id="medicalConditionImageYes" class="btn btn-outline-primary option-btn" data-target="medicalConditionImage" data-value="Yes" style="width:40%; height:40px; margin-right:5px;">Yes</button>
                    <button type="button" id="medicalConditionImageNo" class="btn btn-outline-primary option-btn" data-target="medicalConditionImage" data-value="No" style="width:40%; height:40px; margin-left:5px;">No</button>
                    <div class="text-danger" id="medicalConditionImage-error"></div>

                </div>
            </div>


                <!-- Hidden File Upload initially -->
                <div class="form-group mt-4" id="fileUploadGroup" style="display: none;">               
                     <label for="fileUpload" id="fileUploadButton" style="cursor: pointer;">Upload Image</label>
                    <!-- Hidden file input -->
                    <input type="file" class="form-control d-none" id="fileUpload" name="fileUpload">
                    <span id="file-name" class="text-muted mt-2"></span> 
                    <div class="text-danger" id="fileUpload-error"></div>

                </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="button" id="back-consultDetails" class="btn btn-light btn-block rounded border border-grey">Back</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="submit" id="submit-weight-loss" class="btn btn-dark btn-block">Continue</button>
                    </div>
                </div>
            </div>

         
        </form>

        </div>
    </div>
</div>
    </div>

<div id="paymentRequest">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center"  style="font-weight: 600;">{{$param}}</h3>
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



