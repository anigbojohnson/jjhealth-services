<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"Register")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center"  style="font-weight: bold;">Register</h1>


        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif 

        @if ($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger"> 
                {{ session()->get('error') }}
            </div>
        @endif 
        
            <form method="POST"  class="form-container">
                @csrf

                <div class="row mt-4">
             
                    <div class="col-md-6 ">

                        <div class="form-group">
                            <label for="fname" style="font-weight: 600;" class="form-label">First Name</label>
                            <input id="name" type="text" required name="fname" value="{{ old('fname') }}"  autocomplete="fname" autofocus class="form-control">
                            @if($errors->has('fname'))
                            <span class="text-danger">
                                {{$errors->first('fname')}}
                            </span>      
                            @endif
                        </div>
                    </div>
        
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label for="lname" class="form-label" style="font-weight: 600;">Last Name</label>
                            <input id="lname" type="text" required name="lname" value="{{ old('lname') }}"  autocomplete="lname" autofocus class="form-control">
                            @if($errors->has('lname'))
                            <span class="text-danger">
                                {{$errors->first('lname')}}
                            </span>      
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label" style="font-weight: 600;">Email</label>
                    <input id="email" type="email" name="email"  required value="{{ old('email') }}"  autocomplete="email" class="form-control">
                    @if($errors->has('email'))
                    <span class="text-danger">
                        {{$errors->first("email")}}
                    </span>      
                    @endif
                </div>

                <div class="form-group">
                    <label for="password" class="form-label" style="font-weight: 600;">Password</label>
                    <input id="password" type="password"  required name="password"  autocomplete="new-password" class="form-control">
                    @if($errors->has('password'))
                    <span class="text-danger">
                        {{$errors->first("password")}}
                    </span>      
                    @endif
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label" style="font-weight: 600;">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"  autocomplete="new-password" class="form-control" required>
                    @if($errors->has("password_confirmation"))
                    <span class="text-danger">
                        {{$errors->first("password_confirmation")}}
                    </span>      
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </div>
                <p>If you are registered, <a href="{{ route('login', ['param' =>$param , 'action' => $action]) }}" >click here</a> to login.</p>
                <a href="{{ url('/auth/microsoft/redirect') }}?page={{'register'}}"  class="btn btn-microsoft btn-block border">
                    <i class="fab fa-microsoft"></i> Register with Microsoft
                </a>  
                <a href="{{ url('/auth/google/redirect') }}?page={{'register'}}"  class="btn btn-google btn-block border">
                    <i class="fab fa-google"></i> Register with Google
                </a> 
            </form>
        </div>
    </div>
</div>

@endsection