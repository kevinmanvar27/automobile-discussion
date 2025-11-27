@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Discussion Forum</h1>
        
    </div>
    
    @if($threads->count() > 0)
        <ul class="thread-list">
            @foreach($threads as $thread)
                <li class="thread-item" id="thread-{{ $thread->id }}">
                    <a href="{{ route('threads.show', $thread) }}" style="text-decoration: none; color: inherit;">
                        <div class="thread-title">{{ $thread->subject }}</div>
                        <div class="thread-meta">
                            By {{ $thread->user->name }} | 
                            {{ $thread->created_at->format('M d, Y H:i') }}
                            @auth
                                @if(Auth::id() === $thread->user_id)
                                    <button class="btn btn-sm btn-secondary edit-thread-btn" data-thread-id="{{ $thread->id }}" style="margin-left: 10px;">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No threads yet. <button type="button" class="btn btn-link" onclick="openThreadModal()">Create the first thread</button>.</p>
    @endif
</div>

<!-- Thread Edit Modal -->
<div id="editThreadModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="card" style="position: relative; background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
        <span id="closeEditThreadModal" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2>Edit Thread</h2>
        
        <form id="editThreadForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editThreadId" name="thread_id">
            <div class="form-group">
                <label for="editThreadSubject">Subject</label>
                <input type="text" name="subject" id="editThreadSubject" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="editThreadDescription">Description</label>
                <textarea name="description" id="editThreadDescription" class="form-control" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Thread</button>
            <button type="button" class="btn btn-secondary" id="cancelEditThread">Cancel</button>
        </form>
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
    document.querySelectorAll('.edit-thread-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openEditThreadModal.call(this);
        });
    });
    
    // Close modal functions
    document.getElementById('closeEditThreadModal').addEventListener('click', function() {
        document.getElementById('editThreadModal').style.display = 'none';
    });
    
    document.getElementById('cancelEditThread').addEventListener('click', function() {
        document.getElementById('editThreadModal').style.display = 'none';
    });
    
    // Close the modal when clicking outside of it
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('editThreadModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
    
    // Handle edit form submission
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
                'X-Requested-With': 'XMLHttpRequest'
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
</script>
@endsection