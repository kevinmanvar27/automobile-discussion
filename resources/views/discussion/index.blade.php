@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="card-title mb-0">Discussion Forum</h1>
        @auth
            <button class="btn btn-primary" onclick="openThreadModal()">New Thread</button>
        @endauth
    </div>
    <div class="card-body">
    
    @if($threads->count() > 0)
        <ul class="thread-list">
            @foreach($threads as $thread)
                <li class="thread-item" id="thread-{{ $thread->id }}">
                    <a href="{{ route('threads.show', $thread) }}" class="thread-title">{{ $thread->subject }}</a>
                    <div class="thread-meta">
                        By {{ $thread->user->name }} | 
                        {{ $thread->created_at->format('M d, Y H:i') }}
                        @auth
                            @if(Auth::id() === $thread->user_id)
                                <button class="btn btn-sm btn btn-primary edit-thread-btn" data-thread-id="{{ $thread->id }}" style="margin-left: 10px;">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            @endif
                        @endauth
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-5">
            <h3>No threads yet</h3>
            <p class="mb-4">Be the first to start a discussion!</p>
            @auth
                <button class="btn btn-primary" onclick="openThreadModal()">Create the first thread</button>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to create a thread</a>
            @endauth
        </div>
    @endif
    </div>
</div>

<!-- Thread Edit Modal -->
<div id="editThreadModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Thread</h2>
            <button type="button" class="close" id="closeEditThreadModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editThreadForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editThreadId" name="thread_id">
                <div class="form-group">
                    <label for="editThreadSubject" class="form-label">Subject</label>
                    <input type="text" name="subject" id="editThreadSubject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="editThreadDescription" class="form-label">Description</label>
                    <textarea name="description" id="editThreadDescription" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary mr-2" id="cancelEditThread">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Thread</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Ensure the modal functions are available
    if (typeof openThreadModal !== 'function') {
        // Fallback in case the global functions aren't available
        function openThreadModal() {
            document.getElementById('threadModal').style.display = 'block';
        }
    }
    
    // Function to open the edit modal
    function openEditThreadModal() {
        const threadId = this.getAttribute('data-thread-id');
        
        // Fetch thread data from the edit route
        fetch(`/threads/${threadId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Set the thread ID and content in the modal form
                document.getElementById('editThreadId').value = data.id;
                document.getElementById('editThreadSubject').value = data.subject;
                document.getElementById('editThreadDescription').value = data.description;
                
                // Show the modal
                document.getElementById('editThreadModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading thread data. Please try again.');
            });
    }
    
    // Add event listeners to all edit buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-thread-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openEditThreadModal.call(this);
            });
        });
        
        // Close modal functions
        if (document.getElementById('closeEditThreadModal')) {
            document.getElementById('closeEditThreadModal').addEventListener('click', function() {
                document.getElementById('editThreadModal').style.display = 'none';
            });
        }
        
        if (document.getElementById('cancelEditThread')) {
            document.getElementById('cancelEditThread').addEventListener('click', function() {
                document.getElementById('editThreadModal').style.display = 'none';
            });
        }
        
        // Close the modal when clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editThreadModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
        
        // Handle edit form submission
        if (document.getElementById('editThreadForm')) {
            document.getElementById('editThreadForm').addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const threadId = document.getElementById('editThreadId').value;
                const formData = new FormData(this);
                
                fetch(`/threads/${threadId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the thread content directly in the DOM
                        const threadElement = document.getElementById('thread-' + threadId);
                        const threadTitleElement = threadElement.querySelector('.thread-title');
                        threadTitleElement.textContent = data.thread.subject;
                        
                        // Close the modal
                        document.getElementById('editThreadModal').style.display = 'none';
                        
                    } else {
                        alert('Error updating thread: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating thread. Please try again.');
                });
            });
        }
    });
</script>
@endsection