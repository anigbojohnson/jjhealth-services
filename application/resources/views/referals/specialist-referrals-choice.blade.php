@extends('welcome')
@section('title',"Specialist Referrals choice")
@section('content')


<div class="full-width">
    
    <div class="row section">
        <!-- Image Column -->
        <div class="col-md-10 text-column">   
            <h1>Specialist Referrals</h1>
            <p>JJHealth provides a variety of specialist referrals that you can present to your specialist. If the referral you require is not listed, you can request it during a consultation with a JJHealth doctor.</p>
        </div>
        
        <!-- Text Column -->
        <div class="col-md-6 text-column">  
        </div> 
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

    <div class="row border gy-3 mt-5 section " style="border:1px solid light-blue;margin-right:20px; margin-left:20px;outline:1px solid grey;border-radius: 20px;background-color: #FFF9ED;" >
    @foreach($solutions as $solution)

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5>{{ $solution->solution_name }}</h5>
            <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price ${{ $solution->cost }}</span>
            <hr>
            <p>{{ $solution->description }}</p>
            <form method="POST" action="{{ route('specialist-referrals.request') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $solution->id }}">
                <input type="hidden" name="solution_id" value="{{ $solution->solution_id }}">
                <input type="hidden" name="solution_name" value="{{ $solution->solution_name }}">
                <input type="hidden" name="cost" value="{{ $solution->cost }}">
                <input type="hidden" name="description" value="{{ $solution->description }}">


                <button type="submit" class="btn btn-primary w-100 mt-3">
                    Request {{ $solution->solution_name }}
                </button>
            </form>
          </div>
        </div>
      </div>
      @endforeach
</div>

    <div class="row border gy-3 mt-5 section " style="border:1px solid light-blue;margin-right:20px; margin-left:20px;outline:1px solid grey;border-radius: 20px;background-color: #FFF9ED;" >
        <div class="col-md-3"></div>
      <div class="col-md-5">
            <h4><b>Need something not listed? </b></h4>
            <p>Our doctors can provide referrals to a range of specialists. If you need a referral that's not listed above, click the button below</p>
            <a href="#"  class="btn btn-primary w-100 mt-3">Request {{ $solution->solution_name }}</a>
          
      </div>
     <div class="col-md-3"></div>

</div>
@endsection
