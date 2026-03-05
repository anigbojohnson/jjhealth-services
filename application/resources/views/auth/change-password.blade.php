@extends('welcome')
@section('title',"Login")
@section('content')

<div class="container">


    <div class="row justify-content-center">

        <div class="col-md-8">
        @if(isset($message))
        <div id="error-message" class="alert alert-danger">
            {{ $message }}
        </div>
        @endif
        <div id="success-message" class="alert alert-success" style="display:none;"></div>

            <div class="card">
                <div class="card-header" style="font-weight: bold;"><h4>{{ __('Change Password') }}</h4></div>

                <div class="card-body">
                    <form method="POST"  id="change-password-form"  action="/change-forggotten-password">
                        @csrf
                        <ul id="password-errors" class="text-danger" style="display: none;"></ul> <!-- List for displaying errors -->

                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            <label for="password" style="font-weight: bold;">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" style="font-weight: bold;">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            <span class="text-danger " id="confirm-password-error"></span>

                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Change Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection