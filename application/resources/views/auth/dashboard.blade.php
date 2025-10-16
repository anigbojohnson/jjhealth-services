@extends('welcome')
@section('title',"Dashboard")
@section('content')


    <!-- Left Sidebar -->
    <div class="sidebar">
        <!-- Sidebar content goes here -->
        <ul class="sidebar-menu">
            <li><a href="{{ route('user.account') }}">Dashboard</a></li>
            <li><a href="{{ route('user.account') }}">Dashboard</a></li>
            <li><a href="{{ route('user.account') }}">Dashboard</a></li>

            <!-- Add more sidebar links as needed -->
        </ul>
    
</div>

@endsection