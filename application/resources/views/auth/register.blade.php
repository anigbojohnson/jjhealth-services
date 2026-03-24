<!-- resources/views/auth/register.blade.php -->
@extends('welcome')
@section('title',"Register")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center fw-bold mt-3 mb-3">Register</h1>


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
        
<form method="POST" class="form-container">
    @csrf

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="fname" class="form-label fw-medium">First Name</label>
                <input id="name" type="text"  name="fname" value="{{ old('fname') }}" autocomplete="fname" autofocus class="form-control">
                @error('fname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="lname" class="form-label fw-medium">Last Name</label>
                <input id="lname" type="text"  name="lname" value="{{ old('lname') }}" autocomplete="lname" autofocus class="form-control">
                @error('lname')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="email" class="form-label fw-medium">Email</label>
        <input id="email" type="email" name="email"  value="{{ old('email') }}" autocomplete="email" class="form-control">
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label fw-medium">Password</label>
        <input id="password" type="password"  name="password" autocomplete="new-password" class="form-control">
        @error('password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"  class="form-control">
        @error('password_confirmation')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit button -->
    <div class="mb-3">
        <button type="submit" class="btn btn-primary fw-medium w-100">Sign Up</button>
    </div>

    <!-- Login link -->
    <p class="mb-3 text-center">
        If you are registered, <a href="{{ route('login') }}" class="fw-medium">click here</a> to login.
    </p>

    <!-- Social login buttons -->
    <div class="d-grid gap-2 mb-2">
        <a href="{{ url('/auth/register/microsoft/redirect') }}" class="btn btn-microsoft w-100 border mt-3 mb-3">
            <i class="fab fa-microsoft"></i> Register with Microsoft
        </a>

        <a href="{{ url('/auth/registr/google/redirect') }}" class="btn btn-google w-100 border">
            <i class="fab fa-google"></i> Register with Google
        </a>
    </div>
</form>
        </div>
    </div>
</div>

@endsection