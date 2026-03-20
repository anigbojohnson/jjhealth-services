@extends('welcome')
@section('title', "")
@section('content')
<div class="container">

<hr class="mt-4">

<div class=" center-page row gy-3 mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 d-flex align-items-center justify-content-center">
               <b> <p class="text-center mb-0"> Login or Create Your account </p></b>
            </div>
            <div class="col-md-4 ">
                <a href="{{ route('login') }}" class="btn btn-success w-100 mt-3">Login</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('register') }}" class="btn btn-primary w-100 mt-3">Register</a>
            </div>
        </div>

        </div>
      </div>
    </div>
</div>
</div>

  
@endsection
