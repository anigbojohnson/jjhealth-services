@extends('welcome')
@section('title',"Dashboard")
@section('content')
<div class="dashboard">
    @include('dashboard.sidebar')

    <div class="main-content">
        <h2>Welcome to JJHealth services, {{Auth::user()->first_name}}</h2>
    <div class="account-body">
    <h2>What We Offer</h2>
<a href="{{ route('consult-category') }}" class="card-link">
    <div class="account-body-card">
        <div class="card-image green">
            🩺
        </div>
        <div class="card-content">
            <h3>Telehealth Consultations</h3>
            <p>Lower price, same easy access to the care you need.</p>
        </div>
    </div>
</a>

<a href="{{ route('certificate') }}" class="card-link">
    <div class="account-body-card">
        <div class="card-image teal">
            📄
        </div>
        <div class="card-content">
            <h3>Medical Certificate</h3>
            <p>Get a doctor's certificate online for work, school, or personal leave.</p>
        </div>
    </div>
</a>

<a href="{{ route('referral.specialist-referral.select') }}" class="card-link">
    <div class="account-body-card">
        <div class="card-image yellow">
            🧑‍⚕️
        </div>
        <div class="card-content">
            <h3>Specialist Referral</h3>
            <p>Get referred to the right specialist after an online consultation.</p>
        </div>
    </div>
</a>

<a href="{{ route('pathology.select') }}" class="card-link">
    <div class="account-body-card">
        <div class="card-image orange">
            🧪
        </div>
        <div class="card-content">
            <h3>Pathology Referral</h3>
            <p>Receive a referral for pathology tests from a licensed doctor.</p>
        </div>
    </div>
</a>

<a href="{{ route('weight-loss') }}" class="card-link">
    <div class="account-body-card">
        <div class="card-image purple">
            ⚖️
        </div>
        <div class="card-content">
            <h3>Weight Loss Treatment</h3>
            <p>Access personalised weight management plans with medical support.</p>
        </div>
    </div>
</a>
       </div>
    </div>
</div>



<style>
    .dashboard{display:flex;min-height:80vh;}
    .main-content{flex:1;padding:30px;background:#fff;}
    .content{flex:1;padding:40px;}
    .card{background:#fff;border-radius:12px;padding:20px;margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.08);}
    .account-body{max-width:1100px;margin:40px auto;background:#fff;border:1px solid #ddd;border-radius:20px;padding:40px;}
    .account-body-card{display:flex;border:1px solid #ddd;border-radius:18px;overflow:hidden;margin-bottom:20px;transition:.25s;}
    .account-body-card:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,.08);}
    .card-image{width:240px;display:flex;align-items:center;justify-content:center;font-size:60px;color:#345;}
    .green{background:#c8e39a;}
    .teal{background:#a8e5d2;}
    .yellow{background:#ffd07b;}
    .orange{background:#f59a79;}
    .card-content{flex:1;padding:35px;}
    .card-content p{font-size:24px;color:#555;line-height:1.5;}
    .card-link{display:block;text-decoration:none;color:inherit;}
    .card-link:hover{text-decoration:none;}
    .card-link:hover .account-body-card{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.1);}
</style>
@endsection
