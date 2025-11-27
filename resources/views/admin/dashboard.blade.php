@extends('layouts.admin')

@section('admin-content')
<div class="card">
    <h1>Admin Dashboard</h1>
    
    <h2>Pending Verifications</h2>
    
    @if($users->count() > 0)
        <ul class="thread-list">
            @foreach($users as $user)
                <li class="thread-item">
                    <div class="thread-title">{{ $user->name }}</div>
                    <div class="thread-meta">
                        Shop: {{ $user->shop_name }} | 
                        Email: {{ $user->email }} | 
                        Mobile: {{ $user->mobile_no }} | 
                        City: {{ $user->city }}
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.generate-password', $user) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">Generate Password & Verify</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p>No pending verifications.</p>
    @endif
</div>
@endsection