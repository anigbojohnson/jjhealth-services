@extends('welcome')
@section('title',"Home")
@section('content')
<div style="width: 100%; text-align: center;">
    <img src="{{ asset('images/home-page.png') }}" class="home-headings-image"alt="Home page image">
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
      <div class="col-md-4">
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
  
        <div class="col-md-4">
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

        <div class="col-md-4">
            <div class="card">
                <i class="fa-solid fa-mobile" style="width:13%;height:10%;font-size: 3rem; position: relative; right: -10px; top: 10px;"></i>
                <div class="card-body">
                    <h5>Pathology</h5>
                    <li>For when you need to speak to a doctor</li>
                    <li>Fast access to medical advice</li>    
                    <a href="{{ route('pathology') }}" class="btn btn-primary w-100 mt-3">Request Consultation</a>
                </div>
            </div>
        </div>
    </div>
    
  
    <div class="row gy-3 mt-3">
      <div class="col-md-4">
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

      <div class="col-md-4">
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
</div>

<section class="why-choose-section text-center py-5">
    <div class="container">

        <h2 class="mb-3">Why choose JJHealth?</h2>

        <p class="text-muted mb-5">
            JJHealth is the leading provider of convenient, high-quality telehealth in Australia.
            <br>
            Here's a guide to how it works:
        </p>

        <div class="row g-4">

        <div class="col-md-4">
           <img src="{{ asset('images/choose_jjhealth.png') }}"  class="left-full-img" alt="image">
        </div>
        <div class="col-md-8">
          <div class="row g-4" >

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Experienced Australian doctors</h5>
                    <p>Committed to providing you with the highest quality care.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Secure and private platform</h5>
                    <p>Highest Australian standards for privacy and security.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Fast appointment booking</h5>
                    <p>Book your appointment easily online in minutes.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Affordable pricing</h5>
                    <p>Transparent pricing and various payment options.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Prescriptions and referrals</h5>
                    <p>We provide prescriptions and referrals for pathology and radiology.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box p-4 border rounded">
                    <h5>Medical certificates</h5>
                    <p>Personal or carer’s certificates for work, school or university.</p>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<div class="faq-section py-5 " style="width: 100%;background-color: #FFF9ED;ma">
    <div class="container" >
      


       
<div class="row">
  <div class="col-md-4 d-flex flex-column align-items-center"
     style="border:1px; min-height:100%;">

    <h2 class="text-center mb-4 title">Frequently Asked Questions</h2>

    <div style="width:200px; height:200px;
                display:flex; align-items:center; justify-content:center; 
                text-align:center; padding:20px;">
        <p class="fw-bold m-0">
          Still want help? <a href="/faq">click here</a>
        </p>
    </div>

</div>
      <div class="col-md-8"> 

        <div class="accordion faq-accordion" id="faqAccordion">

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq1">
                        Is my consultation private?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. All consultations are confidential and handled by licensed medical professionals.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq2">
                        Do I need to show any ID or medical history?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Some consultations may require basic identification or relevant medical history.
                    </div>
                </div>
            </div>


            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq1">
                        Is my consultation private?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. All consultations are confidential and handled by licensed medical professionals.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq2">
                        Do I need to show any ID or medical history?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Some consultations may require basic identification or relevant medical history.
                    </div>
                </div>
            </div>


            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq3">
                        Can I get an STD test through this service?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. Our doctors can provide testing guidance and treatment options if necessary.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq4">
                        How soon will I receive my prescription or results?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Prescriptions and results are usually delivered within a short time after the consultation.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq5">
                        Can I speak to a male or female doctor?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. Where possible, you may request a doctor based on your preference.
                    </div>
                </div>
            </div>

        </div>

        </div>
      </div>
       
    </div>
</div> 

  @endsection
