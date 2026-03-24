@extends('welcome')
@section('title',"Weight Loss")
@section('content')
@vite(['resources/js/app.js', 'resources/js/weight-loss.js'])


<div class="full-width-container">
    <div class="relative-container">

            <form method="POST" id='weight-lost-form' action="{{ route('weight-loss-consultation') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $solutions[0]->id }}">
                <input type="hidden" name="solution_id" value="{{ $solutions[0]->solution_id }}">
                <input type="hidden" name="solution_name" value="{{ $solutions[0]->solution_name }}">
                <input type="hidden" name="cost" value="{{ $solutions[0]->cost }}">
                <input type="hidden" name="description" value="{{ $solutions[0]->description }}">


                <button type="submit" class="btn bg-dark btn-primary btn-lg top-button">
                    Get started
                </button>
            </form>

        
        
        <img class="full-width-image" src="{{ asset('images/WL.jpg') }}" alt="weight loss poster">
    </div>
</div>

<div id="home-content" class="container mt-4"> 
    @if(request()->has('messege'))
        <div class="alert alert-success">
            {{ request('messege') }}
        </div>
    @endif
</div>

@if(session()->has('success'))
<div class="alert alert-success">
    {{ session()->get('success') }}
</div>
@endif 
<div id="home-content" class="container"> 
    @if(request()->has('messege'))
        <div class="alert alert-success mt-4">
            {{ request('messege') }}
        </div>
    @endif
</div>

<section>
  <div class="container">
    <div class="row align-items-center g-5">
 
      <!-- Left -->
      <div class="col-lg-6">
        <h1>Clinician-led weight<br/>loss treatment</h1>
        <p class="subtitle">
          Our weight loss programme offers personalised care tailored to your needs, 
          all under the guidance of experienced medical professionals.
        </p>
 
        <ul class="feature-list">
          <li>
            <span class="dot"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            Approved medical treatments
          </li>
          <li>
            <span class="dot"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            Clinically monitored body weight reduction
          </li>
          <li>
            <span class="dot"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            Free MyFitnessPal tracking
          </li>
          <li>
            <span class="dot"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            And more
          </li>
        </ul>
 
        <p class="note">
          With JJHealth , healthcare is convenient, secure, and tailored to your lifestyle — 
          so you can focus on feeling your best.
        </p>

        <p class="fw-medium">${{ $solutions[0]->cost }}. Speak to a doctor today.</p>
            <form method="POST" action="{{ route('weight-loss-consultation') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $solutions[0]->id }}">
                <input type="hidden" name="solution_id" value="{{ $solutions[0]->solution_id }}">
                <input type="hidden" name="solution_name" value="{{ $solutions[0]->solution_name }}">
                <input type="hidden" name="cost" value="{{ $solutions[0]->cost }}">
                <input type="hidden" name="description" value="{{ $solutions[0]->description }}">
                <button type="submit" class="btn-start">
                     Start my journey
                    <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>
      </div>
 
      <!-- Right -->
      <div class="col-lg-6">
        <div class="img-wrap">
          <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=800&q=80" alt="Doctor consultation"/>
        </div>
      </div>
 
    </div>
  </div>
</section>

<div class="row section gy-3 " style="background-color:#f0f8ff;margin-top:5px;">
 
    <h4 class="text-center" style="padding-bottom:20px;">Why JJHealth is prefered for specialist referral </h4>
    <div class="col-md-6">
        <img src="{{ asset('images/specialist-referral.-page-header.jpg') }}" 
        alt="discover" 
        class="card-icon">
     </div>
     <div class="col-md-6 d-flex flex-column gap-3">
        
        <div style="background-color:white;" class="treat-card d-flex align-items-center gap-3">
            <i class="fa-solid fa-id-card icon-style"></i>
            <span class="treat-text">ADHA registered provider</span>
        </div>
    <div style="background-color:white;" class="treat-card d-flex align-items-center gap-3">
        <i class="fa-solid fa-user-doctor icon-style"></i>
        <span class="treat-text">AHPRA registered doctors</span>
    </div>

    <div style="background-color:white;" class="treat-card d-flex align-items-center gap-3">
        <i class="fa-solid fa-ban icon-style"></i>
        <span class="treat-text">No cancellation fees</span>
    </div>

    <div style="background-color:white;" class="treat-card d-flex align-items-center gap-3">
        <i class="fa-solid fa-shield-halved icon-style"></i>
        <span class="treat-text">Safe and secure</span>
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
        <h2 class="text-center mb-4 title">Frequently Asked Questions</h2>

  <div class="col-md-4 d-flex flex-column align-items-center order-md-1 order-2"
     style="border:1px; min-height:100%;">


    <div style="width:200px; height:200px;
                display:flex; align-items:center; justify-content:center; 
                text-align:center; padding:20px;">
        <p class="fw-bold m-0">
          Still want help? <a href="/faq">click here</a>
        </p>
    </div>

</div>
      <div class="col-md-8 order-md-2 order-1"> 

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
                        Is my consultation private?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. All consultations are confidential and handled by licensed medical professionals.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq4">
                        Do I need to show any ID or medical history?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Some consultations may require basic identification or relevant medical history.
                    </div>
                </div>
            </div>


            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq5">
                        Can I get an STD test through this service?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. Our doctors can provide testing guidance and treatment options if necessary.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq6">
                        How soon will I receive my prescription or results?
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Prescriptions and results are usually delivered within a short time after the consultation.
                    </div>
                </div>
            </div>

            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed faq-button" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq7">
                        Can I speak to a male or female doctor?
                    </button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
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
