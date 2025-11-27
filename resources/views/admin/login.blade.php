@extends('layouts.admin')

@section('content')
<div class="card">
    <h1>Admin Login</h1>
    
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Admin Login</button>
    </form>
    
    <p><a href="{{ route('login') }}">User Login</a></p>
</div>
@endsection