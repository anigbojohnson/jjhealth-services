@extends('welcome')
@section('title', "Payment")
@section('content')

<div id="home-content"> 
    <div class="justify-content-center mt-4">
        <h2>Payment</h2>
        <hr>  
    </div>
    <div class="row gy-3 mt-5">
        @foreach ($solutions as $solution)
            <div class="col-md-4">
                <div class="card">
                    <img class="card-img-top" src="..." alt="Card image cap">
                    <div class="card-body">
                        <h5>{{ $solution->solution_name }}</h5>
                        <p>{{ $solution->description }}</p>
                        <span style="background-color: lightblue; font-weight: bold;" class="text-white rounded px-2 py-1">
                            cost: {{ $solution->cost }}
                        </span>
                        <form action="{{ route('payment') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="solution_id" value="{{ $solution->solution_id }}">
                            <input type="hidden" name="solution_name" value="{{ $solution->solution_name }}">
                            <input type="hidden" name="description" value="{{ $solution->description }}">
                            <input type="hidden" name="cost" value="{{ $solution->cost }}">
                            <button type="submit" class="btn btn-primary w-100">Pay</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
