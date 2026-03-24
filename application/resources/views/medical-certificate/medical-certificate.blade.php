@extends('welcome')
@section('title',"Medical certificate")
@section('content')

@vite(['resources/js/app.js', 'resources/js/medical-certificate.js'])

<div style="width: 100%;">
    <img src="{{ asset('images/MC-home.png') }}" alt="MC home image" style="width:100%; padding:0; height: auto; max-width: 100%; margin:0;">
</div>
@php
    $mc01= $solutions->firstWhere('solution_id', 'MC01');
    $mc02= $solutions->firstWhere('solution_id', 'MC02');
    $mc03= $solutions->firstWhere('solution_id', 'MC03');
    $mc04= $solutions->firstWhere('solution_id', 'MC04');
@endphp

<div id="home-content" class="container"> 
      @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
      @endif
    <div  class=" justify-content-center  mt-4">
      <h2>Medical Certificates</h2>
      <p>
        <i>
            If you need to take a sick day or time off from work, school, or university, you can request a medical certificate from home. Personal and Carer’s certificates are available. Simply complete an online questionnaire about your condition, and if approved, our doctors will email the certificate to you
        </i>      
      </p>  
    </div>
        @if(request()->has('messege'))
            <div class="alert alert-success">
                {{ request('messege') }}
            </div>
        @endif
    <div class="row gy-3 mt-5">
      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top"  src="{{ asset('images/MC-work.jpg') }}"  alt="Card image cap">
          <div class="card-body">
            <h5>Work</h5>
            <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price: ${{ $mc01->cost }}</span>
            <a href="{{ route('medical-certificate', ['param' =>  str_replace(' ', ' ','Medical Certificate For Work'), 'action' => 'work-medical-certificate']) }}"  class="btn btn-primary w-100 mt-3">Request Work Certificate</a>
          </div>
        </div>
      </div>
      
        <div class="col-md-4">
          <div class="card">
            <img class="card-img-top" src="{{ asset('images/MC-School.png') }}"  alt="Card image cap">
            <div class="card-body">
              <h5>School</h5>
              <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price: ${{ $mc02->cost }}</span>
  
              <a href="{{ route('medical-certificate', ['param' =>  str_replace(' ', ' ','Medical Certificate For School'),'action'=>'studies-medical-certificate']) }}" class="btn btn-primary w-100 mt-3">Request School Certificate</a>
            </div>
          </div>
        </div>
  
        
            <div class="col-md-4">
              <div class="card">
                <img class="card-img-top" src="{{ asset('images/MC-holiday-traveller.jpg') }}" alt="Card image cap">
                <div class="card-body">
                  <h5>Travel and holiday Cancellation</h5>
                  <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price: ${{ $mc04->cost }}</span>
                  <a href="{{ route('medical-certificate', ['param' =>  str_replace(' ', ' ', 'Request Travel and holiday Certificate'),'action'=>'travel-and-holiday-certificate']) }}" class="btn btn-primary w-100 mt-3">Request Travel and holiday Certificate</a>

                </div>
              </div>
            </div>   
        </div>
    <div class="row gy-3 mt-5">
        <div class="col-md-4">
            <div class="card">
                <img class="card-img-top" src="{{ asset('images/MC-carer.jpg') }}" alt="Card image cap">
                <div class="card-body">
                    <h5>Carer's Leave</h5>
                
                    <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price: ${{ $mc03->cost }}</span>
        
                    <a href="{{ route('medical-certificate', ['param' =>  str_replace(' ', ' ','Request Carer Leave Certificate'),'action'=>'carers-Leave-certificate']) }}" class="btn btn-primary w-100 mt-3">Request Carer's Leave Certificate</a>
                </div>
            </div>
        </div>      
    </div>
  </div>


    
<div class="row mt-5 " style="background-color: #f2f2f2; ">
  <div>

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

<div class="faq-section py-5" style="background-color: #FFF9ED;width: 100%;">
    <div class="container" >

        <h2 class="text-center mb-5 title">Frequently Asked Questions</h2>

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
   
@endsection
