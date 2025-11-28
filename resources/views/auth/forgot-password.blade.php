@extends('layouts.app')

@section('content')
<div class="card mx-auto auth-form-card" style="max-width: 500px; margin-top: 2rem; margin-bottom: 2rem;">
    <div class="card-header auth-form-header text-white text-center">
        <h1 class="card-title mb-0">Reset Password</h1>
    </div>
    <div class="card-body">
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="form-group mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control form-control-lg" value="{{ old('email') }}" required>
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Send Password Reset Link</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0"><a href="{{ route('login') }}">Back to Login</a></p>
    </div>
    </div>
</div>
@endsection