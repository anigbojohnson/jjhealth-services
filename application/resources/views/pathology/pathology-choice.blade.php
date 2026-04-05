@extends('welcome')
@section('title',"Pathology referral choices")
@section('content')

@vite(['resources/js/app.js', 'resources/js/pathology-referrals.js'])

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

     @php
        $solutionImages = [
            'TR01' => '', 'TR02' => '', 'TR03' => 'Sore Throat.jpeg', 'TR04' => '', 'TR05' => 'Asthma & COPD.jpg',
            'TR06' => 'Sinus Infection.jpg', 'TR07' => '', 'TR08' => 'Dental Infection.jpg', 'TR09' => '', 'TR10' => '',
            'TR11' => '', 'TR12' => 'Gout.avif', 'TR13' => 'Bacterial Vaginosis.jpg', 'TR14' => '', 'TR15' => 'Breast Feeding.jpg',
            'TR16' => 'Skin Infections.jpg', 'TR17' => 'Herpes & Cold Sores.jpg', 'TR18' => '', 'TR19' => '', 'TR20' => 'Eczema & Psoriasis.jpg',
            'TR21' => '', 'TR22' => '', 'TR23' => '',
        ];

   @endphp

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

@foreach ($solutions as $solution)

    @php
        $image = $solutionImages[$solution->solution_id] ?? '';
        $imagePath = $image ? asset('images/treatment/'.$image) : asset('images/treatment/discover-icon.png');
    @endphp
<div class="col">
    <div class="card pathology-card h-100 pathology-card"
         style="cursor:pointer;"
         data-id="{{ $solution->id }}"
         data-solution-id="{{ $solution->solution_id }}"
         data-name="{{ $solution->solution_name }}"
         data-cost="{{ $solution->cost }}"
         data-description="{{ $solution->description }}">

        <img src="{{ $imagePath }}" 
             class="card-img-top pathology-img" 
             alt="{{ $solution->solution_name }}">

        <div class="card-body text-center">

            <h4 class="pathology-title">{{ $solution->solution_name }}</h4>

            <p class="pathology-desc">
                {{ $solution->description }}
            </p>

            <h5 class="pathology-price">
                ${{ $solution->cost }}<sup>00</sup>
            </h5>

        </div>
    </div>
</div>
@endforeach

</div>
</div>

<style>
    .pathology-card{border:none;border-radius:40px;overflow:hidden;background:#f5f5f5;transition:all 0.3s ease;}
    .pathology-card:hover{transform:translateY(-5px);}
    .pathology-img{width:100%;height:220px;object-fit:cover;border-top-left-radius:40px;border-top-right-radius:40px;}
    .pathology-title{font-weight:600;margin-top:10px;}
    .pathology-desc{color:#6c757d;font-size:15px;}
    .pathology-price{margin-top:10px;font-weight:600;}
</style>

<script>
    
$(document).ready(function () {

$(document).on('click', '.pathology-card', function () {
        console.log('JS loaded');
    let id = $(this).data('id');
    let solutionId = $(this).data('solution-id');
    let solutionName = $(this).data('name');
    let cost = $(this).data('cost');
    let description = $(this).data('description');




    $.ajax({
        url: '/pathology-referrals/request',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: id,
            solution_id: solutionId,
            solution_name: solutionName,
            cost: cost,
            description: description
        },
        success: function (response) {
            // handle success (redirect or message)
            window.location.href = response.redirect_url;
        },
        error: function (err) {
            window.location.href = response.redirect_url;

        }
    });

});

});

</script>
@endsection
