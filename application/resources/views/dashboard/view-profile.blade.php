@extends('welcome')
@section('title',"My Profile")
@section('content')
<div class="dashboard" >
@include('dashboard.sidebar')
    {{-- Main Content --}}
    <div class="main-content">

        <div class="profile-header">
            <div>
                <h2>My Profile</h2>
                <p>View and manage your personal information.</p>
            </div>
        </div>

        <div id="success-message"
            class="success-message"
            style="display:none;">
        </div>

        <div class="profile-card">

            {{-- Profile heading --}}
        <div class="header-top">
            <div class="profile-top">
                
                    @if(Auth::user()->profile_picture)

                        <img
                            src="{{ Storage::disk('s3')->temporaryUrl(
                                Auth::user()->profile_picture,
                                now()->addMinutes(10)
                            ) }}"                            
                             alt="Profile picture"
                            class="profile-avatar"
                        >
                    @else
                        <div class="profile-avatar">
                            {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                        </div>
                    @endif 

                <div>
                    <h3>
                        {{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}
                    </h3>
                    <p>{{ Auth::user()->email }}</p>
                </div>

            </div>
            <div class="header-action">
                <a href="{{ route('edit-profile') }}" class="btn-primary">
                    + Edit profile
                </a>
            </div>
        </div>

            {{-- Personal Information --}}
            <div class="profile-section">

                <h3>Personal Information</h3>

                <div class="profile-grid">


                    <div class="profile-field">
                        <label>First Name</label>
                        <div class="field-value">
                            {{ Auth::user()->first_name }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Last Name</label>
                        <div class="field-value">
                            {{ Auth::user()->last_name }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Email Address</label>
                        <div class="field-value">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Phone Number</label>
                        <div class="field-value">
                            {{ Auth::user()->phone ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Date of Birth</label>
                        <div class="field-value">
                            {{ Auth::user()->date_of_birth ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Gender</label>
                        <div class="field-value">
                            {{ Auth::user()->gender ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Indigene</label>
                        <div class="field-value">
                            {{ Auth::user()->indigene ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Address</label>
                        <div class="field-value">
                            {{ Auth::user()->address ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Information --}}
            <div class="profile-section">

                <h3>Account Information</h3>

                <div class="profile-grid">

                    <div class="profile-field">
                        <label>Account Status</label>

                        <div class="field-value">
                            <span class="status-badge">
                                Active
                            </span>
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Member Since</label>

                        <div class="field-value">
                            {{ Auth::user()->created_at->format('d M Y') }}
                        </div>
                    </div>

                </div>  
            </div>

    @if(Auth::user()->provider == "form register")
        {{-- Account Security --}}
        <div class="profile-section">

            <h3>Account Security</h3>

            <div class="profile-grid">

                <div class="profile-field">
                    <label>Password</label>

                    <div class="field-value">
                        ••••••••••••••••
                    </div>
                </div>

                <div class="profile-field">
                    <label>Security</label>

                    <div class="field-value">
                        Keep your account secure by regularly updating your password.
                    </div>
                </div>

            </div>

            <div class="profile-actions">
                <a href="{{ route('change-password') }}" class="btn-primary">
                    Change Password
                </a>
            </div>


        </div>
        @else
   
        @endif 

        </div>

    </div>

</div>
<script>
    $(document).ready(function () {
        const message = sessionStorage.getItem('profileMessage');

        if (message) {

            $('#success-message')
                .text(message)
                .css('display', 'flex');

            sessionStorage.removeItem('profileMessage');
        }

    });
</script>
<style>       
.header-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;background:#F7FBF9;border-bottom:1px solid #e5e7eb;padding:30px;}
.main-content{flex:1;background:#fff;padding:20px;}
.dashboard {display: flex;min-height: 80vh;}
.header-action{display:flex;justify-content:flex-end;}
.sidebar {width: 240px;background: #E8F7EF;border-right: 1px solid #e5e7eb;padding: 18px;}
.section-title {font-size: 12px;font-weight: bold;color: #7d7d7d;margin-bottom: 12px;margin-top: 10px;letter-spacing: 1px;}
.menu-item {display: block;padding: 12px 14px;margin-bottom: 8px;text-decoration: none;color: #333;border-radius: 8px;transition: .2s;}
.menu-item:hover {background: #d7eee3;}
.menu-item.active {background: #D7EEE3;color: #245C42;font-weight: 600;}
.main-content {flex: 1;padding: 35px;background: #fff;}
.profile-header {margin-bottom: 25px;}
.profile-header h2 {margin: 0 0 6px;font-size: 28px;color: #222;}
.profile-header p {margin: 0;color: #666;}
.profile-card{max-width:1000px;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;}
.profile-top{display:flex;align-items:center;gap:18px;}
.profile-avatar{width:70px;height:70px;display:flex;align-items:center;justify-content:center;background:#D7EEE3;color:#245C42;border-radius:50%;font-size:26px;font-weight:700;}
.profile-top h3{margin:0 0 5px;font-size:21px;}
.profile-top p{margin:0;color:#666;}
.profile-section{padding:30px;border-bottom:1px solid #eee;}
.profile-section h3{margin:0 0 22px;font-size:18px;color:#333;}
.profile-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:22px;}
.profile-field label{display:block;margin-bottom:7px;font-size:13px;font-weight:600;color:#777;}
.field-value{padding:12px 14px;background:#f8faf9;border:1px solid #e5e7eb;border-radius:9px;color:#333;}
.status-badge{display:inline-block;padding:5px 12px;background:#D7EEE3;color:#245C42;border-radius:20px;font-size:13px;font-weight:600;}
.profile-actions{display:flex;justify-content:flex-end;padding:22px 30px;}
.btn-primary{display:inline-block;padding:12px 20px;background:#245C42;color:white;text-decoration:none;border-radius:9px;font-weight:600;}
.btn-primary:hover{background:#1d4c36;}
@media(max-width:768px){.dashboard{flex-direction:column;}.sidebar{width:auto;}.profile-grid{grid-template-columns:1fr;}.main-content{padding:20px;}}
.success-message {display: none;margin-bottom: 25px;padding: 14px 18px;background: #E8F7EF;border: 1px solid #B9DEC9;border-radius: 10px;color: #245C42;font-size: 14px;font-weight: 500;align-items: center;gap: 10px;}
.success-message::before {content: "✓";width: 22px;height: 22px;display: flex;align-items: center;justify-content: center;background: #D7EEE3;border-radius: 50%;font-size: 13px;font-weight: 700;}
</style>
@endsection
