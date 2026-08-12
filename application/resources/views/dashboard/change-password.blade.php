@extends('welcome')
@section('title',"change password")
@section('content')
@vite(['resources/js/app.js', 'resources/js/manage-profile.js'])

<div class="dashboard">
@include('dashboard.sidebar')
<main class="main-content">
    <div class="profile-header">
        <div>
            <h2>Change Password</h2>
            <p>Update your password to keep your account secure.</p>
        </div>
    </div>

    <div class="profile-card">

        <form 
              id="change-password-form">
            @csrf

            <div class="profile-section">

                <div class="password-form">

                    {{-- Current Password --}}
                    <div class="profile-field">
                        <label for="current_password">
                            Current Password
                        </label>

                        <div class="password-input">
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                autocomplete="current-password"
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="current_password"
                            >
                                👁
                            </button>
                        </div>

                        <small
                            id="current_password-error"
                            class="error"
                        ></small>
                    </div>


                    {{-- New Password --}}
                    <div class="profile-field">
                        <label for="password">
                            New Password
                        </label>

                        <div class="password-input">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password"
                            >
                                👁
                            </button>
                        </div>

                        <small
                            id="password-error"
                            class="error"
                        ></small>
                    </div>


                    {{-- Confirm Password --}}
                    <div class="profile-field">
                        <label for="password_confirmation">
                            Confirm New Password
                        </label>

                        <div class="password-input">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_confirmation"
                            >
                                👁
                            </button>
                        </div>
                        <small
                            id="password_confirmation-error"
                            class="error"
                        ></small>
                    </div>
                    {{-- Password Requirements --}}
                    <div class="password-requirements">

                        <strong>Password requirements</strong>

                        <ul>
                            <li>At least 8 characters</li>
                            <li>Contains at least one uppercase letter</li>
                            <li>Contains at least one lowercase letter</li>
                            <li>Contains at least one number</li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- Success Message --}}
            <div
                id="success-message"
                class="success-message"
                style="display:none;"
            ></div>

            {{-- Actions --}}
            <div class="profile-actions">
                <a
                    href="{{ route('view-profile') }}"
                    class="btn-secondary"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="btn-primary"
                    id="change-password-button"
                    class="btn-primary"
                >
                    Change Password
                </button>
            </div>
        </form>
    </div>
</main>

</div>
<style>

.main-content{flex:1;background:#fff;padding:20px;}
.profile-card{max-width:1000px;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;padding:25px;}
.password-form {max-width: 650px;}
.btn-primary{display:inline-block;padding:12px 20px;background:#245C42;color:white;text-decoration:none;border:none;border-radius:9px;font-weight:600;cursor:pointer;margin-left: 20px;}
.btn-primary:hover{background:#1d4c36;}
.btn-secondary{display:inline-block;padding:12px 20px;background:#f3f5f4;color:#333;text-decoration:none;border:1px solid #ddd;border-radius:9px;font-weight:600;}
.btn-secondary:hover{background:#e9ecea;}
.profile-header{margin-bottom:25px;}
.profile-header h2{margin:0 0 6px;font-size:28px;color:#222;}
.profile-header p{margin:0;color:#666;}
.password-input {position: relative;display: flex;align-items: center;margin-top: 10px;}
.password-input input {width: 100%;padding: 12px 45px 12px 14px;border: 1px solid #e5e7eb;border-radius: 9px;font-size: 14px;outline: none;}
.password-input input:focus {border-color: #245C42;}
.toggle-password {position: absolute;right: 12px;border: none;background: transparent;cursor: pointer;font-size: 16px;}
.password-requirements {margin-top: 20px;margin-bottom: 20px;padding: 15px;background: #f8faf9;border-radius: 9px;color: #666;font-size: 13px;}
.password-requirements strong {color: #333;}
.password-requirements ul {margin: 8px 0 0;padding-left: 20px;}
.password-requirements li {margin-bottom: 4px;}
.profile-field {margin-top: 10px;}
.error {display: block;margin-top: 6px;color: #dc2626;font-size: 13px;}
.success-message {margin: 20px 30px;padding: 12px 15px;background: #D7EEE3;color: #245C42;border: 1px solid #b8dfcc;border-radius: 9px;font-size: 14px;}
    </style>
@endsection
