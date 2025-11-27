@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Register</h1>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="shop_name">Shop Name</label>
            <input type="text" name="shop_name" id="shop_name" class="form-control" value="{{ old('shop_name') }}" required>
            @error('shop_name')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="mobile_no">Mobile Number</label>
            <input type="text" name="mobile_no" id="mobile_no" class="form-control" value="{{ old('mobile_no') }}" required>
            @error('mobile_no')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" required>
            @error('city')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
            @error('address')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
</div>
@endsection