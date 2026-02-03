@extends('welcome')
@section('title',"Home")
@section('content')
<div style="width: 100%; text-align: center;">
    <img src="{{ asset('images/home-page.png') }}" alt="Home page image" style="width: 100%; height: 650px; max-width: 100%;">
</div>
<div class="container">
     


    <div  class="container container-fluid d-flex justify-content-center align-items-center mt-4">
      <h3>How can we help you today?</h3>

    </div>
    @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    <div class="row gy-3 mt-5">
      <div class="col-md-6">
        <div class="card">
          <img class="card-img-top" src="{{ asset('images/MC_Logo.png') }}"  style="width:7%;font-size: 3rem; position: relative; right: -10px; top: 10px;">
          <div class="card-body">
            <h5>Medical Certificates</h5>
            <li>For work, uni, school or carers</li>
            <li>Sent to your email in minutes</li>    

            <a href="{{ route('certificate') }}" class="btn btn-primary w-100 mt-3">Request Certificates</a>
          </div>
        </div>
      </div>
  
      <div class="col-md-6">
        <div class="card">
        <i class="fa-solid fa-mobile" style="width:13%;height:10%;font-size: 3rem; position: relative; right: -10px; top: 10px;"></i>
          <div class="card-body">
            <h5>Telehealth Consultations</h5>
            <li>For when you need to speak to a doctor</li>
            <li>Fast access to medical advice</li>    
            <a href="{{ route('telehealth') }}" class="btn btn-primary w-100 mt-3">Request Consultation</a>
          </div>
        </div>
      </div>
    </div>
    
  
    <div class="row gy-3 mt-3">
      <div class="col-md-6">
        <div class="card">
        <i class="fa-solid fa-weight-scale" style="font-size: 3rem; position: relative; right: -10px; top: 10px;"></i> <!-- Adjust position here -->
          <div class="card-body">
            <h5>Weight Loss Treatment</h5>
            <li>Weight loss medical management</li>
            <li>Doctor consults & treatment options</li>    
            <a href="{{ route('weight-loss') }}" class="btn btn-primary w-100 mt-3">Request Consultation</a>
          </div>
        </div>
      </div>
    
      <div class="col-md-6">
        <div class="card">
        <i class="fa-solid fa-user-doctor" style="font-size: 3rem; position: relative; right: -10px; top: 10px;"></i>
          <div class="card-body">
            <h5>Specialist Referrals</h5>
            <li>For skin checks, eye tests and more</li>
            <li>Referrals sent to your email</li>    
            <a href="{{ route('specialist-referral-home') }}"  class="btn btn-primary w-100 mt-3">Request Referrals</a>
          </div>
        </div>
      </div>
    </div>
    <h2 class="faq-header" style="margin-top:100px;">Frequently Asked Questions</h2>
    
    <div id="faq" class="accordion">
        <!-- FAQ Item 1 -->
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq1">
                <span class="faq-question">What is JJHealth Services?</span>
                <i class="fa fa-chevron-down icon"></i>
            </div>
            
            <div id="faq1" class="collapse faq-content ">
                <p>A medical certificate, also called a doctor’s certificate or sick note, is an official document issued by a licensed online medical practitioner that verifies a patient's medical condition. It may include details such as the diagnosis, the severity of the illness or injury, and the expected recovery time. Medical certificates are often used to support sick leave requests, disability claims, or insurance applications. Employers, schools, and other organizations may require them as proof of legitimate absence due to illness.</p>
                <p>If you work for an Australian employer, you may need to provide a valid medical certificate if you're unable to work due to illness. Employees must inform their employer as soon as possible that they are unfit for work and need sick or carer’s leave, even if this is after the leave has begun. It's important to indicate the expected duration of the absence.</p>
                 <p>If an employee fails to provide evidence when requested, they may lose the right to be paid for their sick or carer’s leave.</p>
              </div>
        </div>
        
        <!-- FAQ Item 2 -->
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq2">
                <span class="faq-question">How much does it cost?</span>
                <i class="fa fa-chevron-down icon"></i>
            </div>
            <div id="faq2" class="collapse faq-content">
                <p>An employer may ask an employee to provide evidence confirming that their leave was taken for the following reasons:</p>
                <ul>
                    <li>Inability to work due to illness or injury</li>
                    <li>The need to care for or support an immediate family or household member affected by illness, injury, or an unexpected emergency</li>
                </ul>
                <p>Employers are allowed to request evidence for even a single day or less of absence.</p>
                <p>If the employee fails to provide the required evidence, they may not qualify for paid sick or carer’s leave.</p>
                <p>Awards or registered agreements may specify when employees need to submit evidence and what type is acceptable. Any requested evidence must be reasonable based on the circumstances.</p>
            </div>
        </div>

        <!-- FAQ Item 3 -->
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq3">
                <span class="faq-question">Who can use JJHealth Services?</span>
                <i class="fa fa-chevron-down icon"></i>
            </div>
            <div id="faq3" class="collapse faq-content">
                <p>The issuance of a medical certificate depends on the doctor's assessment of the presented issue and its clinical appropriateness. This evaluation is based on the information provided in the online medical questionnaire, followed by a telephone or video consultation with the doctor.</p>
                <p>Please note that not all medical certificate requests will be approved. If necessary, you may be advised to visit a local General Practitioner for an in-person examination.</p>
            </div>
        </div>

        <!-- FAQ Item 4 -->
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq4">
                <span class="faq-question">Are InstantScripts doctors real doctors?</span>
                <i class="fa fa-chevron-down icon"></i>
            </div>
            <div id="faq4" class="collapse faq-content">
                <p>When applying for a medical certificate online, it’s important to note that these certificates are legally recognized in Australia as valid evidence for sick or carer’s leave. Employers, insurance companies, and other organizations may accept online medical certificates as proof of illness or injury.</p>
                <p>The doctor will assess the issue to determine if issuing a medical certificate is clinically appropriate. Not all requests will be approved, and in some cases, you may be advised to see a local GP for an in-person evaluation.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq4">
                <span class="faq-question">What happen to my personal data?</span>
                <i class="fa fa-chevron-down icon"></i>
            </div>
            <div id="faq4" class="collapse faq-content">
                <p>When applying for a medical certificate online, it’s important to note that these certificates are legally recognized in Australia as valid evidence for sick or carer’s leave. Employers, insurance companies, and other organizations may accept online medical certificates as proof of illness or injury.</p>
                <p>The doctor will assess the issue to determine if issuing a medical certificate is clinically appropriate. Not all requests will be approved, and in some cases, you may be advised to see a local GP for an in-person evaluation.</p>
            </div>
        </div>
    </div>
  </div>

  @endsection
