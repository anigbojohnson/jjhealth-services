@extends('welcome')
@section('title',"Specialist Referrals")
@section('content')

<div class="full-width-container">
    <div class="relative-container">
        <a style=" width:30%;" href="{{ route('specialist-referrals-request', ['param' =>  str_replace(' ', ' ', 'Specialist Referrals'),'action'=>'special-referals']) }}" class="btn bg-dark btn-primary btn-lg top-button">Get started</a>
        <img class="full-width-image" src="{{ asset('images/WL.jpg') }}" alt=" specialist  referrals poster">
    </div>
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
  @endsection
 