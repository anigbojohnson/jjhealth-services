@extends('welcome')
@section('title',"Medical certificate")
@section('content')
<div style="width: 100%; text-align: center;">
    <img src="{{ asset('images/MC-home.png') }}" alt="MC home image" style="width: 100%; height: 450px; max-width: 100%;">
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
        <h2 class="faq-header" style="margin-top:100px;">Frequently Asked Questions</h2>
    
    <div id="faq" class="accordion">
        <!-- FAQ Item 1 -->
        <div class="faq-item">
            <div class="faq-button" data-toggle="collapse" data-target="#faq1">
                <span class="faq-question">What is an online medical certificate?</span>
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
                <span class="faq-question">Do I need a medical certificate?</span>
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
                <span class="faq-question">How are medical certificates approved?</span>
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
                <span class="faq-question">Are online medical certificates legal in Australia?</span>
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
