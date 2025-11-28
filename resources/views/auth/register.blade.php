@extends('layouts.app')

@section('content')
<div class="card mx-auto auth-form-card" style="max-width: 600px; margin-top: 2rem; margin-bottom: 2rem;">
    <div class="card-header auth-form-header text-white text-center">
        <h1 class="card-title mb-0">Register</h1>
    </div>
    <div class="card-body">
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ old('name') }}" required>
                @error('name')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <input type="text" name="shop_name" id="shop_name" class="form-control form-control-lg" value="{{ old('shop_name') }}" required>
                @error('shop_name')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="mobile_no" class="form-label">Mobile Number</label>
                <input type="text" name="mobile_no" id="mobile_no" class="form-control form-control-lg" value="{{ old('mobile_no') }}" required>
                @error('mobile_no')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
                @error('email')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" name="city" id="city" class="form-control form-control-lg" value="{{ old('city') }}" required>
                @error('city')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control form-control-lg" rows="3" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="alert alert-error mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Register</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
    </div>
    </div>
</div>
@endsection