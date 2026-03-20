@extends('welcome')
@section('title',"Doctor Consultation")
@section('content')
@vite(['resources/js/app.js', 'resources/js/telehealth-consultation.js'])

<div style="width: 100%; text-align: center;">
    <img src="{{ asset('images/p.png') }}" alt="Home page image" style="width: 100%; height: 650px; max-width: 100%;">
</div>
<div class="container mt-5">
<h2 style="text-align: center;" class="mt-4 mb-4">How Can We Help You Today?</h2>
<p style="text-align: center;" >If you're experiencing chest pain, shortness of breath, or any symptoms requiring urgent medical attention, please visit the nearest emergency department or call 000 to request an ambulance.</p>

        <div class="row">
            <!-- Option 1: I am feeling unwell -->
            <div class="col-md-12 mb-3">
                <a href="{{ route('consult-category') }}"  style="text-decoration: none;" class="option-card d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Feeling Unwell" class="option-icon">
                        <span class="ms-3 option-text">I need to treat medical condition</span>
                    </div>
                    <span class="arrow-icon">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>


    <div class="row vh-90 section">
        <h2 class="title h2 text-center">How it Works</h2>
        <p class="text-center">Your health and privacy come first. Our licensed doctors provide expert care for sexual health concerns — from STI testing and treatment to contraception and performance support — all through discreet, affordable telehealth appointments, on your schedule and from the comfort of home.</p>

      <!-- Left Panel -->
        <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-start text-white bg-dark p-5"
           style="background-image: url('your-image.jpg'); background-size: cover; background-position: center;">
        <h2 class="display-6 fw-semibold">Use Image Here</h2>
      </div>

      <!-- Right Panel -->
      <div class="col-12 col-md-6 d-flex flex-column justify-content-center p-5 bg-light">
        <div id="step-content">
          <!-- Step content gets updated here by JavaScript -->
        </div>

        <!-- Navigation Buttons -->
        <div id="stepNav" class="mt-4 d-flex justify-content-between">
          <button id="prevBtn" class="btn btn-outline-secondary text-uppercase fw-semibold" onclick="goToPrevious()" style="display: none;">
            &larr; Previous
          </button>
          <button id="nextBtn" class="btn btn-link text-uppercase fw-semibold text-dark ps-0" onclick="goToNext()">
            Next &rarr;
          </button>
        </div>
      </div>
    </div>
  </div>
</div>



 <div class="faq-section py-5 " style="width: 100%;background-color: #FFF9ED;">
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
