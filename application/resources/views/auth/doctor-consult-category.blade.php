@extends('welcome')
@section('title',"Doctor Consultation")
@section('content')
<div class="container my-5">
        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif 
        @if(request()->has('messege'))
                <div class="alert alert-success">
                    {{ request('messege') }}
                </div>
            @endif
    <h2 class="text-center mb-4">Choose a category from the options below</h2>

        <!-- Search Form -->
    <div class="text-center mb-4">
        <form  action="{{ route('search-solutions') }}" method="GET" class="d-flex justify-content-center">
            <input type="text" name="query" class="form-control me-2" placeholder="Search categories" aria-label="Search">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    @if($message)
         <div class="alert alert-info text-center">{{ $message }}</div>
    @endif


    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($solutions as $solution)
            <div class="col mt-4">
                <a href="{{ route('telehealth-consultation', ['param' => str_replace(' ', ' ', $solution->id.'~~'.$solution->solution_name)]) }}" style="color:black; text-decoration:none;">
                    <div class="card h-100 option-card">
                        <div class="card-body text-center">
                            <img src="" class="option-icon mb-3" alt="{{ $solution->solution_name }} Icon">
                            <h5 class="card-title">${{$solution->cost }}<sup>00</sup></h5>
                            <h4 class="option-title">{{ $solution->solution_name }}</h4>
                            <p class="card-text">{{ $solution->description }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
