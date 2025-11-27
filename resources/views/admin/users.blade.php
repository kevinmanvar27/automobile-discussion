@extends('layouts.admin')

@section('content')
<div class="card">
    <h1>All Users</h1>
    
    <table id="usersTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Shop Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>City</th>
                <th>Verified</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->shop_name }}</td>
                    <td>{{ $user->mobile_no }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->verified ? 'Yes' : 'No' }}</td>
                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if(!$user->verified)
                            <form method="POST" action="{{ route('admin.generate-password', $user) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Generate Password</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "order": [[ 0, "desc" ]], // Order by ID descending by default
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    });
</script>
@endsection