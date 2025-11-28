@extends('layouts.admin')

@section('admin-content')
<div class="admin-card">
    <div class="admin-card-header">
        <h1 class="admin-card-title mb-0">Admin Dashboard</h1>
    </div>
    <div class="admin-card-body">
        <h2 class="mb-4">Pending Verifications</h2>
        
        @if($users->count() > 0)
            <div class="admin-table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Shop Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->shop_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile_no }}</td>
                                <td>{{ $user->city }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.generate-password', $user) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-success admin-btn-sm">Generate Password & Verify</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <h3>No pending verifications</h3>
                <p class="mb-0">All users are verified.</p>
            </div>
        @endif
    </div>
</div>
@endsection