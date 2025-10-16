@extends('welcome')
@section('title',"Login")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

           
            <h1 class="text-center"  style="font-weight: bold;">Login</h1>
            @error('error')
            <p class="text-danger text-center" >{{ $message }}</p>
            @enderror 

            @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif 
        
        @if(session()->has('error'))
            <div class="alert alert-danger"> {{-- Change 'alert-success' to 'alert-danger' --}}
                {{ session()->get('error') }}
            </div>
        @endif 
        
        
            <form method="POST" action="{{ route('login', ['param' =>  str_replace(' ', ' ',$param), 'action'=>$action ]) }}" class="form-container">
                @csrf

        
                <div class="form-group">
                    <label for="email" class="form-label font-weight-bold">Email</label>
                    <input id="email" type="text" name="email" value="{{ old('email') }}"  autocomplete="email" class="form-control">
   
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label font-weight-bold">Password</label>
                        <span><a  href="#" data-toggle="modal" data-target="#exampleModal">
                            Forgotten Password?</a></span>
                    </div>

                    <input id="password" type="password" name="password"  autocomplete="new-password" class="form-control">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
            </form>
            <p>If you are not registered, <a href="{{ route('register', ['param' =>$param , 'action' => $action]) }}">click here</a> to register.</p>

            <a href="{{ url('/auth/microsoft/redirect') }}?page={{'login'}}" class="btn btn-microsoft btn-block border">
                <i class="fab fa-microsoft"></i> Login with Microsoft
            </a>  
            <a href="{{ url('/auth/google/redirect') }}?page={{'login'}}"   class="btn btn-google btn-block border">
                <i class="fab fa-google"></i> Login with Google
            </a> 
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Forgotten Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="forgottenMessage" class="container d-flex justify-content-center"></div>
        <div class="modal-body">
            <form id='forgotten-password' class="form-container">
                @csrf
           
            <div class="form-group">
                <label for="email" class="font-weight-bold form-label">Email</label>
                <input id="email"  type="text" name="email" value="{{ old('email') }}"  autocomplete="email" class="form-control">

                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
           
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send</button>
        </div>
    </form>

      </div>

    </div>
  </div>
</div>
@endsection
