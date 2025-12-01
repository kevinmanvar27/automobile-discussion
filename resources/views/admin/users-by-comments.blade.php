@extends('layouts.admin')

@section('admin-content')
<div class="admin-card">
    <div class="admin-card-header">
        <h1 class="admin-card-title mb-0">Users by Comment Count</h1>
        <p class="mb-0">List of all users sorted by their total comment count in descending order</p>
    </div>
    <div class="admin-card-body">
        <!-- Added wrapper div for horizontal scrolling -->
        <div class="admin-table-responsive">
            <table id="usersByCommentsTable" class="admin-table display p-3" style="width:100%">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Shop Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>Comments Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->shop_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile_no }}</td>
                            <td>{{ $user->city }}</td>
                            <td>{{ $user->comments_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersByCommentsTable').DataTable({
            "order": [[ 6, "desc" ]], // Order by comments count descending by default
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "columnDefs": [
                { "orderable": false, "targets": [0] } // Disable sorting on Rank column
            ],
            "searching": true,     // Enable search functionality
            "paging": true,        // Enable pagination
            "info": true,          // Enable info display
            "autoWidth": false,    // Disable auto width calculation
            "responsive": true     // Enable responsive behavior
        });
    });
</script>
@endpush