@extends('welcome')
@section('title', "Frequently Asked Questions")
@section('content')

<div class="consultation-container">
    <h2 class="text-center mb-5 title">Frequently Asked Questions</h2>

<!-- Tabs Navigation (Desktop) -->
<div class="tabs-section d-none d-md-block">
    <ul class="nav justify-content-center flex-wrap gap-4 mb-4">
        <li class="nav-item">
            <a href="#speak-to-doctor" class="nav-link tab-link active" data-bs-toggle="tab">Speak to a doctor</a>
        </li>
        <li class="nav-item">
            <a href="#prescriptions" class="nav-link tab-link" data-bs-toggle="tab">Prescriptions</a>
        </li>
        <li class="nav-item">
            <a href="#medical-certificates" class="nav-link tab-link" data-bs-toggle="tab">Medical certificates</a>
        </li>
        <li class="nav-item">
            <a href="#pathology" class="nav-link tab-link" data-bs-toggle="tab">Pathology</a>
        </li>
        <li class="nav-item">
            <a href="#specialist-referrals" class="nav-link tab-link" data-bs-toggle="tab">Specialist referrals</a>
        </li>
    </ul>
</div>


<!-- Dropdown (Mobile) -->
<div class="tabs-section d-md-none mb-4">
    <select class="form-select" id="mobileTabSelect">
        <option value="#speak-to-doctor">Speak to a doctor</option>
        <option value="#prescriptions">Prescriptions</option>
        <option value="#medical-certificates">Medical certificates</option>
        <option value="#pathology">Pathology</option>
        <option value="#specialist-referrals">Specialist referrals</option>
    </select>
</div>

    <!-- Tab Content -->
    <div class="tab-content mt-5">

        <!-- Speak to a Doctor -->
        <div class="tab-pane fade show active" id="speak-to-doctor">
            <div class="accordion faq-accordion section" id="faqDoctor">
                @php
                    $doctorFAQs = [
                        ["Is my consultation private?", "Yes. All consultations are confidential and handled by licensed medical professionals."],
                        ["Do I need to show any ID or medical history?", "Some consultations may require basic identification or relevant medical history."],
                        ["Can I get an STD test through this service?", "Yes. Our doctors can provide testing guidance and treatment options if necessary."],
                        ["How soon will I receive my prescription or results?", "Prescriptions and results are usually delivered within a short time after the consultation."],
                        ["Can I speak to a male or female doctor?", "Yes. Where possible, you may request a doctor based on your preference."]
                    ];
                @endphp

                @foreach($doctorFAQs as $index => $faq)
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#doctorFaq{{ $index }}">
                            {{ $faq[0] }}
                        </button>
                    </h2>
                    <div id="doctorFaq{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqDoctor">
                        <div class="accordion-body">
                            {{ $faq[1] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="tab-pane fade" id="prescriptions">
            <div class="accordion faq-accordion section" id="faqPrescriptions">
                @foreach($doctorFAQs as $index => $faq)
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#prescriptionFaq{{ $index }}">
                            {{ $faq[0] }}
                        </button>
                    </h2>
                    <div id="prescriptionFaq{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqPrescriptions">
                        <div class="accordion-body">
                            {{ $faq[1] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

       <!-- Medical Certificates -->
<div class="tab-pane fade" id="medical-certificates">
    <div class="accordion faq-accordion section" id="faqCertificates">
        @php
            $certificateFAQs = [
                ["Are medical certificates legally valid?", "Yes. Certificates are issued by registered medical professionals and are recognized by employers, schools, and insurance companies."],
                ["How long does it take to receive a certificate?", "Certificates are usually delivered digitally within hours of your consultation."],
                ["Can I get a certificate for travel or fitness purposes?", "Yes. Our doctors can provide certificates for sick leave, travel, fitness, or other clinically appropriate reasons."],
                ["Is my medical information kept confidential?", "Absolutely. All information included in certificates is handled securely and confidentially."],
                ["Can I request a specific doctor for my certificate?", "Where possible, you can request a doctor, and certificates will be issued accordingly."]
            ];
        @endphp

        @foreach($certificateFAQs as $index => $faq)
        <div class="accordion-item faq-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#certificateFaq{{ $index }}">
                    {{ $faq[0] }}
                </button>
            </h2>
            <div id="certificateFaq{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqCertificates">
                <div class="accordion-body">
                    {{ $faq[1] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Pathology -->
<div class="tab-pane fade" id="pathology">
    <div class="accordion faq-accordion section" id="faqPathology">
        @php
            $pathologyFAQs = [
                ["What pathology tests are available?", "Blood tests, urine tests, and other laboratory investigations are available as recommended by your doctor."],
                ["How do I get a pathology test?", "Your doctor will provide a requisition form that can be used at any accredited pathology lab."],
                ["How long do results take?", "Results typically take 24–48 hours, with urgent tests sometimes available within hours."],
                ["Can the doctor interpret my results?", "Yes. Follow-up consultations can include detailed interpretation of your results."],
                ["Is my pathology information confidential?", "All your pathology information is kept secure and confidential in compliance with privacy laws."]
            ];
        @endphp

        @foreach($pathologyFAQs as $index => $faq)
        <div class="accordion-item faq-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#pathologyFaq{{ $index }}">
                    {{ $faq[0] }}
                </button>
            </h2>
            <div id="pathologyFaq{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqPathology">
                <div class="accordion-body">
                    {{ $faq[1] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Specialist Referrals -->
<div class="tab-pane fade" id="specialist-referrals">
    <div class="accordion faq-accordion section" id="faqReferrals">
        @php
            $referralFAQs = [
                ["Which specialists can I be referred to?", "We can refer you to cardiologists, dermatologists, orthopedics, neurologists, and more based on your condition."],
                ["How soon will I receive a referral?", "Referrals are issued immediately after your consultation if clinically appropriate and delivered digitally."],
                ["Can I request a specific specialist?", "Yes. You may request a specific specialist and our doctors will make recommendations accordingly."],
                ["Are referrals recognized by insurance companies?", "Yes. Most insurance plans require a valid referral to cover specialist visits."],
                ["Is my referral information confidential?", "All referral information is handled securely and in compliance with privacy regulations."]
            ];
        @endphp

        @foreach($referralFAQs as $index => $faq)
        <div class="accordion-item faq-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse" data-bs-target="#referralFaq{{ $index }}">
                    {{ $faq[0] }}
                </button>
            </h2>
            <div id="referralFaq{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#faqReferrals">
                <div class="accordion-body">
                    {{ $faq[1] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
    


<style>
    .consultation-container {  }
    .tab-link { font-size: 16px; font-weight: 500; color: #666 !important; text-decoration: none; border-bottom: 2px solid transparent; transition: 0.3s; }
    .tab-link.active { color: #0052cc !important; border-bottom-color: #0052cc !important; }
    .faq-section { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto; }
    .faq-title { font-size: 28px; font-weight: 700; color: #333; margin-bottom: 30px; text-align: center; }
    .faq-paragraph { font-size: 16px; line-height: 1.8; color: #555; text-align: justify; }
    @media (max-width:768px){ .consultation-container{padding:20px 15px;} .tab-link{font-size:14px;} .faq-section{padding:25px;} .faq-title{font-size:22px;} .faq-paragraph{font-size:14px; text-align:left;} 
    .section {padding: 0 !important;margin: 0 !important;background: none !important;}}
</style>


<script>
document.getElementById('mobileTabSelect').addEventListener('change', function () {

    let target = this.value;

    // remove active classes
    document.querySelectorAll('.tab-pane').forEach(function(tab){
        tab.classList.remove('show','active');
    });

    // show selected tab
    let selectedTab = document.querySelector(target);
    selectedTab.classList.add('show','active');

});
</script>
@endsection

