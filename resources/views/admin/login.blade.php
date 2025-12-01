@extends('layouts.admin')

@section('admin-content')
<div class="d-flex align-items-center justify-content-center min-vh-100 admin-login-container">
    <div class="card w-100" style="max-width: 500px;">
        <!-- Header -->
        <div class="admin-card-header">
            <h1 class="admin-card-title text-center mb-0">Admin Login</h1>
        </div>

        <!-- Body -->
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.login') }}" class="admin-login-form">
                @csrf

                <!-- Email -->
                <div class="admin-form-group">
                    <label for="email" class="admin-form-label">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="admin-form-control" 
                        value="{{ old('email') }}" 
                        required
                    >
                    @error('email')
                        <div class="admin-alert admin-alert-error mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="admin-form-group">
                    <label for="password" class="admin-form-label">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="admin-form-control" 
                        required
                    >
                    @error('password')
                        <div class="admin-alert admin-alert-error mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="d-grid mt-4">
                    <button type="submit" class="admin-btn admin-btn-primary admin-btn-lg">
                        Admin Login
                    </button>
                </div>
            </form>

            <!-- User Login Link -->
            <div class="text-center mt-4">
                <p>
                    <a href="{{ route('login') }}" class="admin-btn admin-btn-link">
                        User Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection