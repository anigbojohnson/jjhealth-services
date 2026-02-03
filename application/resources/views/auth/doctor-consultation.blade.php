@extends('welcome')
@section('title',"Doctor Consultation")
@section('content')
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

          

            <!-- Option 3: I need a medical certificate -->
            <div class="col-md-12 mb-3">
                <a href="{{ route('certificate') }}" style="text-decoration: none;" class="option-card d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Medical Certificate" class="option-icon">
                        <span class="ms-3 option-text">I need a medical certificate</span>
                    </div>
                    <span class="arrow-icon">&rarr;</span>
                </a>
            </div>

            <!-- Option 4: I am looking for weight loss treatment -->
            <div class="col-md-12 mb-3">
                <a href="{{ route('weight-loss') }}" style="text-decoration: none;" class="option-card d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center">
                        <img src="https://via.placeholder.com/40" alt="Weight Loss" class="option-icon">
                        <span class="ms-3 option-text">I am looking for a weight loss treatment</span>
                    </div>
                    <span class="arrow-icon">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
@endsection
