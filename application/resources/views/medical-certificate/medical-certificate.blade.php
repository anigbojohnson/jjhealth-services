@extends('welcome')
@section('title',"Medical certificate")
@section('content')

@vite(['resources/js/app.js', 'resources/js/medical-certificate.js'])

<div style="width: 100%; width:auto; height:auto;">
    <img src="{{ asset('images/medical-certificate/MC-home.png') }}" alt="MC home image" style="width:100%; padding:0; height: auto; max-width: 100%; margin:0;">
</div>

{{-- Build paired single/multiple collections before the loop --}}
@php
$singles = $solutions->whereIn('solution_id', ['MC01', 'MC02', 'MC03', 'MC04'])->values();
      $images = [
        7 => 'work.jpg',
        8 => 'school.jpg',
        9 => 'carer.jpg',
        10 => 'holiday and traveller.jpg',
    ];
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
    @foreach ($singles as $index => $solution)
        @php
            $cardId   = $solution->id;
        @endphp
 
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
 
                {{-- Image --}}
                <div style="position: relative;">
                    <img class="card-img-top"
                         src="{{ asset('images/medical_certificate/' . $images[$solution->category_id]) }}"
                         alt="{{ $solution->solution_name }}"
                         style="height: 180px; object-fit: cover; width: 100%;">
                    <span style="position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,0.92); border-radius: 999px; padding: 3px 12px; font-size: 11px; font-weight: 600; color: #185FA5; letter-spacing: 0.04em;">
                        MEDICAL CERTIFICATE
                    </span>
                </div>
 
                <div class="card-body px-3 pt-3 pb-4">
 
                    {{-- Title + Price --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="mb-0 fw-semibold" id="title-{{ $cardId }}" style="font-size: 17px;">
                            {{ $solution->solution_name }}
                        </h5>
                        <span id="price-{{ $cardId }}" class="fw-semibold" style="font-size: 13px; color: #185FA5; background: #E6F1FB; padding: 3px 12px; border-radius: 999px;">
                            ${{ $solution->cost }}
                        </span>
                    </div>
 
                    {{-- Description --}}
                    <p id="desc-{{ $cardId }}" class="text-muted mb-3" style="font-size: 12.5px; line-height: 1.5; min-height: 36px;">
                        {{ $solution->description }}
                    </p>
 
                    {{-- Toggle --}}
                    <div class="d-flex justify-content-center mb-3">
                        <div class="billing-toggle-wrap w-100">
                            <div class="toggle-pill" id="pill-{{ $cardId }}"></div>
                            <button class="toggle-btn active w-50 btn-single"
                                    data-card="{{ $cardId }}"
                                    data-solution='@json($solution)'
                                    data-solutions='@json($solutions)'>
                                Single Day
                            </button>
                            <button class="toggle-btn w-50 btn-multiple"
                                    data-card="{{ $cardId }}"
                                    data-solution='@json($solution)'
                                    data-solutions='@json($solutions)'>
                                Multiple Days
                            </button>
                        </div>
                    </div>
 
                    {{-- CTA --}}
                    <a id="cta-{{ $cardId }}"
                        href="#"
                        class="btn w-100 fw-semibold cta-btn"
                        data-solution='@json($solution)'
                        style="background: #185FA5; color: #fff;">
                        Request {{ $solution->solution_name }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
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
   
{{-- JS cardMeta: pairs single (MC01–MC04) with multiple (MC05–MC08) --}}
<script>

$(document).on('click', '.btn-single, .btn-multiple', function () {

    const cardId   = $(this).data('card');
    const mode     = $(this).hasClass('btn-single') ? 'single' : 'multiple';
    const solution = $(this).data('solution');
    const solutions = $(this).data('solutions');

    const pill      = $('#pill-' + cardId);
    const btnSingle = $('.btn-single[data-card="' + cardId + '"]');
    const btnMulti  = $('.btn-multiple[data-card="' + cardId + '"]');
    const priceEl   = $('#price-' + cardId);
    const descEl    = $('#desc-' + cardId);
    const titleEl   = $('#title-' + cardId);
    const ctaEl     = $('#cta-' + cardId);

    let data;

    if (mode === 'single') {
        // UI toggle
        pill.css({
            transform: 'translateX(0)',
            width: btnSingle.outerWidth()
        });

        btnSingle.addClass('active');
        btnMulti.removeClass('active');

        data = solution;

    } else {
        // UI toggle
        pill.css({
            transform: `translateX(${btnSingle.outerWidth() + 2}px)`,
            width: btnMulti.outerWidth()
        });

        btnMulti.addClass('active');
        btnSingle.removeClass('active');

        // find matching "multiple"
        data = solutions.find(item =>
            item.category_id === solution.category_id &&
            item.id !== solution.id
        );
    }

    // update UI
    if (data) {
        priceEl.text('$' + data.cost);
        descEl.text(data.description);
        titleEl.text(data.solution_name);

        // 🔥 VERY IMPORTANT: update CTA data
        ctaEl.text('Request ' + data.solution_name);
        ctaEl.data('solution', data);
    }
});


// ✅ CTA CLICK
$(document).on('click', '.cta-btn', function (e) {
    e.preventDefault();

    const solution = $(this).data('solution');

    handleCtaClick(solution);
    
});


// ✅ YOUR FUNCTION
function handleCtaClick(solution) {
    console.log(solution);

    $.ajax({
    type: 'POST',
    url: '/medical-certificate/request',
    data: JSON.stringify(solution),
    contentType: 'application/json',   // FIXED
    processData: false,

    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    success: function(response) {
        window.location.href = response.redirect_url;
    },
    error: function(xhr) {
        console.error(xhr.responseText);
    }
});

}
 



    window.addEventListener('load', function () {
        setTimeout(function () {
            document.querySelectorAll('.billing-toggle-wrap').forEach(function (wrap) {
                const pill      = wrap.querySelector('.toggle-pill');
                const btnSingle = wrap.querySelector('.toggle-btn.active');
                if (pill && btnSingle) {
                    pill.style.width = btnSingle.offsetWidth + 'px';
                }
            });
        }, 50);
    });
/*



function handleCtaClick(solution) {
        const solution = $(this).data('solution');

    console.log(solution); // already a JS object



    const cardId = el.dataset.cardId;
    const mode   = activeMode[cardId] || 'single'; // default to single if not set yet
    const meta   = cardMeta[cardId][mode];
    const payload = {
        id: data.id,
        solution_id: cardId,
        cost:   meta.price,
        solution_name:   meta.title,
        description:   meta.desc,
    };




}
*/
</script>
 

<style>
    .billing-toggle-wrap {
        display: inline-flex;
        align-items: center;
        background: #f1f1f1;
        border-radius: 999px;
        padding: 4px;
        position: relative;
        gap: 2px;
    }

    .billing-toggle-wrap .toggle-pill {
        position: absolute;
        top: 4px;
        left: 4px;
        height: calc(100% - 8px);
        background: #1a1a1a;
        border-radius: 999px;
        transition: transform 0.25s cubic-bezier(.4,0,.2,1), width 0.25s cubic-bezier(.4,0,.2,1);
        z-index: 0;
    }

    .billing-toggle-wrap .toggle-btn {
        position: relative;
        z-index: 1;
        background: transparent;
        border: none;
        padding: 7px 0;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        color: #999;
        transition: color 0.2s;
        white-space: nowrap;
        text-align: center;
    }

    .billing-toggle-wrap .toggle-btn.active {
        color: #fff;
    }
</style>
@endsection
