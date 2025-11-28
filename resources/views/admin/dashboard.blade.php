@extends('layouts.admin')

@section('admin-content')
<div class="admin-card">
    <div class="admin-card-header">
        <h1 class="admin-card-title mb-0">Admin Dashboard</h1>
    </div>
    <div class="admin-card-body">
        <!-- Statistics Section -->
        <div class="admin-stats mb-4">
            <div class="admin-stat-card">
                <div class="admin-stat-number">{{ $totalUsers }}</div>
                <div class="admin-stat-label">Total Users</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-number">{{ $verifiedUsers }}</div>
                <div class="admin-stat-label">Verified Users</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-number">{{ $totalThreads }}</div>
                <div class="admin-stat-label">Total Threads</div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-number">{{ $totalComments }}</div>
                <div class="admin-stat-label">Total Comments</div>
            </div>
        </div>

        <!-- Pending Verifications Table -->
        <h2 class="mb-4">Pending Verifications</h2>
        
        @if($pendingUsers->count() > 0)
            <div class="admin-table-responsive">
                <table id="pendingUsersTable" class="admin-table display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Shop Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUsers as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
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

        <!-- Recent Threads Table -->
        <h2 class="mt-5 mb-4">Recent Threads</h2>
        
        @if($recentThreads->count() > 0)
            <div class="admin-table-responsive">
                <table id="recentThreadsTable" class="admin-table display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Author</th>
                            <th>Comments</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentThreads as $key => $thread)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $thread->subject }}</td>
                                <td>{{ $thread->user->name }}</td>
                                <td>{{ $thread->comments->count() }}</td>
                                <td>{{ $thread->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <h3>No threads found</h3>
                <p class="mb-0">There are no discussion threads yet.</p>
            </div>
        @endif

        <!-- Recent Comments Table -->
        <h2 class="mt-5 mb-4">Recent Comments</h2>
        
        @if($recentComments->count() > 0)
            <div class="admin-table-responsive">
                <table id="recentCommentsTable" class="admin-table display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Content</th>
                            <th>Author</th>
                            <th>Thread</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentComments as $key => $comment)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ Str::limit($comment->content, 50) }}</td>
                                <td>{{ $comment->user->name }}</td>
                                <td>{{ Str::limit($comment->thread->subject, 30) }}</td>
                                <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <h3>No comments found</h3>
                <p class="mb-0">There are no comments yet.</p>
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTables for all tables
        $('#pendingUsersTable').DataTable({
            "order": [[ 0, "asc" ]],
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
        
        $('#recentThreadsTable').DataTable({
            "order": [[ 0, "asc" ]],
            "pageLength": 5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
        
        $('#recentCommentsTable').DataTable({
            "order": [[ 0, "asc" ]],
            "pageLength": 5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
        });
    });
</script>
@endsection