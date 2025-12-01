@extends('layouts.app')

@section('content')
<div class="card mx-auto auth-form-card" style="max-width: 500px; margin-top: 2rem; margin-bottom: 2rem;">
    <div class="card-header auth-form-header text-white text-center">
        <h1 class="card-title mb-0 text-white text-center">User Login</h1>
    </div>
    <div class="card-body">
        
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
            @error('email')
                <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control form-control-lg" required>
            @error('password')
                <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-between align-items-center mb-3">
            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 flex-md-grow-0">Login</button>
            <a href="{{ route('admin.login') }}" class="btn btn-link">Admin Login</a>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
    </div>
    </div>
</div>
@endsection