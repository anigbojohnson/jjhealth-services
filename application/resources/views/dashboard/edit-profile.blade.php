@extends('welcome')

@section('title', 'Edit Profile')

@section('content')
@vite(['resources/js/app.js', 'resources/js/manage-profile.js'])

<div class="dashboard">

    @include('dashboard.sidebar')

    <div class="main-content">

        <div class="profile-header">
            <div>
                <h2>Edit Profile</h2>
                <p>Update your personal information below.</p>
            </div>
        </div>

            <div class="profile-top">
                
                <div class="profile-avatar-wrapper">
        <form method="POST" 
              id="edit-profile-form-data" 
              enctype="multipart/form-data" 
              action="{{ route('edit-profile') }}"
              >

                @csrf
                @method('POST')

                    @if(Auth::user()->profile_picture)
                        <img
                            src="{{ Storage::disk('s3')->url(Auth::user()->profile_picture) }}"
                            alt="Profile picture"
                            class="profile-avatar"
                        >
                    @else
                        <div class="profile-avatar">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                        </div>
                    @endif     
                    <label for="profile_picture" class="profile-picture-edit">
                        ✎
                    </label>

                    <input
                        type="file"
                        id="profile_picture"
                        name="profile_picture"
                        accept="image/jpeg,image/png,image/webp"
                        hidden
                    >
                    <small id="profile_picture-error" class="error"></small> 

                </div>

            </div>
        <div class="profile-card">


                <div class="profile-section">

                    <h3>Personal Information</h3>

                    <div class="profile-grid">

                        <div class="profile-field">
                            <label for="first_name">First Name</label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name', Auth::user()->first_name) }}"
                            >
                                <small id="fname-error" class="error"></small>        
                        </div>


                        <div class="profile-field">
                            <label for="last_name">Last Name</label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name', Auth::user()->last_name) }}"
                            >
                           <small id="lname-error" class="error"></small> 
                        </div>


                        <div class="profile-field">
                            <label for="email">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', Auth::user()->email) }}"
                            >
                           <small id="email-error" class="error"></small> 
                        </div>


                        <div class="profile-field">
                            <label for="phone_number">Phone Number</label>
                            <input
                                type="text"
                                id="phone_number"
                                name="phone_number"
                                value="{{ old('phone_number', Auth::user()->phone_number) }}"
                            >
                            <small id="pnumber-error" class="error"></small>
                        </div>


                        <div class="profile-field">
                            <label for="dob">Date of Birth</label>
                            <input
                                type="date"
                                id="dob"
                                name="dob"
                                value="{{ old('dob', Auth::user()->dob) }}"
                            >
                            <small id="dob-error" class="error"></small>
                        </div>


                        <div class="profile-field">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'selected' : '' }}>
                                    Male
                                </option>
                                <option value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'selected' : '' }}>
                                    Female
                                </option>
                                <option value="not say" {{ old('gender', Auth::user()->gender) == 'not say' ? 'selected' : '' }}>
                                    Prefer not to say
                                </option>
                            </select>
                            <small id="gender-error" class="error"></small>
                        </div>


                        <div class="profile-field">
                            <label for="indigene">Indigenous Status</label>

                            <select id="indigene" name="indigene">
                                <option value="">Select an option</option>

                                <option value="not say"
                                    {{ old('indigene', Auth::user()->indigene) == 'not say' ? 'selected' : '' }}>
                                    Prefer not to say
                                </option>

                                <option value="no"
                                    {{ old('indigene', Auth::user()->indigene) == 'no' ? 'selected' : '' }}>
                                    No
                                </option>

                                <option value="Aboriginal"
                                    {{ old('indigene', Auth::user()->indigene) == 'Aboriginal' ? 'selected' : '' }}>
                                    Aboriginal
                                </option>

                                <option value="Torres Strait Islander origin"
                                    {{ old('indigene', Auth::user()->indigene) == 'Torres Strait Islander origin' ? 'selected' : '' }}>
                                    Torres Strait Islander
                                </option>
                            </select>

                            <small id="indigene-error" class="error"></small>

                        </div>


                        <div class="profile-field">
                            <label for="address">Address</label>

                            <input
                                type="text"
                                id="address"
                                name="address"
                                value="{{ old('address', Auth::user()->address) }}"
                            >
                            <small id="address-error" class="error"></small>

                        </div>

                    </div>

                </div>


                <div class="profile-actions">

                    <a href="{{ route('view-profile') }}" class="btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn-primary">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<style>    
.dashboard{display:flex;min-height:80vh;}
.profile-header{margin-bottom:25px;}
.profile-header h2{margin:0 0 6px;font-size:28px;color:#222;}
.profile-header p{margin:0;color:#666;}
.profile-card{max-width:1000px;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;}
.profile-section{padding:30px;border-bottom:1px solid #eee;}
.profile-section h3{margin:0 0 22px;font-size:18px;color:#333;}
.profile-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:22px;}
.profile-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:600;color:#777;}
.profile-field input{width:100%;box-sizing:border-box;padding:12px 14px;background:#f8faf9;border:1px solid #dfe5e2;border-radius:9px;color:#333;font-size:15px;outline:none;}
.profile-field input:focus{border-color:#245C42;box-shadow:0 0 0 2px rgba(36,92,66,.08);}
.error{display:block;margin-top:5px;color:#dc3545;font-size:12px;}
.profile-actions{display:flex;justify-content:flex-end;gap:12px;padding:22px 30px;}
.btn-primary{display:inline-block;padding:12px 20px;background:#245C42;color:white;text-decoration:none;border:none;border-radius:9px;font-weight:600;cursor:pointer;}
.btn-primary:hover{background:#1d4c36;}
.btn-secondary{display:inline-block;padding:12px 20px;background:#f3f5f4;color:#333;text-decoration:none;border:1px solid #ddd;border-radius:9px;font-weight:600;}
.btn-secondary:hover{background:#e9ecea;}
.main-content{flex:1;background:#fff;padding:20px;}
.profile-field select{width:100%;box-sizing:border-box;padding:12px 14px;background:#f8faf9;border:1px solid #dfe5e2;border-radius:9px;color:#333;font-size:15px;outline:none;}
.profile-field select:focus{border-color:#245C42;box-shadow:0 0 0 2px rgba(36,92,66,.08);}
.profile-top{display:flex;align-items:center;gap:18px;margin-bottom:15px;}
.profile-top h3{margin:0 0 5px;font-size:21px;}
.profile-top p{margin:0;color:#666;}
.profile-avatar{width:70px;height:70px;display:flex;align-items:center;justify-content:center;background:#D7EEE3;color:#245C42;border-radius:50%;font-size:26px;font-weight:700;}
.profile-avatar-wrapper {position: relative;width: 80px;height: 80px;}
.profile-avatar {width: 80px;height: 80px;object-fit: cover;border-radius: 50%;border: 3px solid #fff;box-shadow: 0 2px 8px rgba(0,0,0,.12);}
.profile-picture-edit {position: absolute;right: -2px;bottom: -2px;width: 28px;height: 28px;display: flex;align-items: center;justify-content: center;background: #245C42;color: #fff;border-radius: 50%;border: 2px solid #fff;font-size: 14px;cursor: pointer;}
.profile-picture-edit:hover {background: #1d4c36;}
</style>

@endsection