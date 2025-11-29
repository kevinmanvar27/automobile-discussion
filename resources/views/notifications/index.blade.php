@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Notifications</h4>
                    @if($notifications->count() > 0)
                        <button id="mark-all-read" class="btn btn-sm btn-primary">Mark All as Read</button>
                    @endif
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group" id="notifications-list">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->is_read ? '' : 'bg-light' }}" data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p class="mb-1">{{ $notification->message }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->is_read)
                                            <button class="btn btn-sm btn-outline-secondary mark-as-read" data-notification-id="{{ $notification->id }}">Mark as Read</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <p class="text-center">No notifications found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Ensure modal functions are available on this page
    if (typeof openThreadModal !== 'function') {
        function openThreadModal() {
            document.getElementById('threadModal').style.display = 'block';
        }
    }
    
    if (typeof closeThreadModal !== 'function') {
        function closeThreadModal() {
            document.getElementById('threadModal').style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle Mark All as Read button
        document.getElementById('mark-all-read').addEventListener('click', function() {
            const url = "{{ route('notifications.read-all') }}";
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect changes
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Handle individual Mark as Read buttons
        document.querySelectorAll('.mark-as-read').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-notification-id');
                
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to reflect changes
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
        
        // Handle thread form submission (needed for New Thread modal)
        if (document.getElementById('threadForm')) {
            document.getElementById('threadForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route("threads.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Check if the response is ok (status in the range 200-299)
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to the newly created thread page
                        window.location.href = '/threads/' + data.thread.id;
                    } else {
                        // Handle errors
                        alert('Error creating thread: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error creating thread. Please try again.');
                });
            });
        }
    });
</script>
@endsection