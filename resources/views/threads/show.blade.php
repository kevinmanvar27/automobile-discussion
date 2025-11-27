@extends('layouts.app')

@section('content')
<div class="card">
    <h1>{{ $thread->subject }}</h1>
    <div class="thread-meta">
        By {{ $thread->user->name }} | 
        {{ $thread->created_at->format('M d, Y H:i') }}
    </div>
    <div style="margin-top: var(--spacing-md); padding: var(--spacing-md); background-color: var(--gray-50); border-radius: var(--radius-md);">
        {{ $thread->description }}
    </div>
</div>

<div class="card">

    <div id="commentsContainer">
        @foreach($thread->comments as $comment)
            <div class="comment" id="comment-{{ $comment->id }}">
                <div class="thread-meta">
                    By {{ $comment->user->name }} | 
                    {{ $comment->created_at->format('M d, Y H:i') }}
                    @auth
                        @if(Auth::id() === $comment->user_id)
                            <button class="btn btn-sm btn-secondary edit-comment-btn" data-comment-id="{{ $comment->id }}" style="margin-left: 10px;">
                                <i class="fas fa-pencil-alt"></i> <!-- Pencil icon instead of text -->
                            </button>
                        @endif
                    @endauth
                </div>
                <div class="comment-content" style="margin-top: var(--spacing-sm);">
                    {{ $comment->content }}
                </div>
            </div>
        @endforeach
    </div>
    
    <h3>Add a Comment</h3>
    <form id="commentForm">
        @csrf
        <div class="form-group">
            <textarea name="content" class="form-control" rows="3" placeholder="Write your comment here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Comment</button>
        <!-- Visual indicator for successful comment addition -->
        <span id="commentSuccessIndicator" style="display: none; color: var(--success); margin-left: 10px;">✓</span>
    </form>
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fff; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 5px;">
        <span class="close" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h2>Edit Comment</h2>
        <form id="editCommentForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editCommentId" name="comment_id">
            <div class="form-group">
                <label for="editCommentContent">Comment:</label>
                <textarea id="editCommentContent" name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Comment</button>
                <button type="button" class="btn btn-secondary cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Handle comment form submission
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const successIndicator = document.getElementById('commentSuccessIndicator');
        
        // Disable the submit button during submission
        submitButton.disabled = true;
        
        fetch('{{ route("comments.store", $thread) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
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
                // Add the new comment to the comments container
                const commentsContainer = document.getElementById('commentsContainer');
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.id = 'comment-' + data.comment.id;
                newComment.innerHTML = `
                    <div class="thread-meta">
                        By ${data.comment.user.name} | 
                        Just now
                        <button class="btn btn-sm btn-secondary edit-comment-btn" data-comment-id="${data.comment.id}" style="margin-left: 10px;">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                    <div class="comment-content" style="margin-top: var(--spacing-sm);">
                        ${data.comment.content}
                    </div>
                `;
                commentsContainer.appendChild(newComment);
                
                // Add event listener to the new edit button
                newComment.querySelector('.edit-comment-btn').addEventListener('click', openEditModal);
                
                // Clear the form
                this.reset();
                
                // Show success indicator with blink effect
                successIndicator.style.display = 'inline';
                successIndicator.style.opacity = 1;
                
                // Blink effect
                let blinkCount = 0;
                const blinkInterval = setInterval(() => {
                    successIndicator.style.opacity = successIndicator.style.opacity == 1 ? 0 : 1;
                    blinkCount++;
                    if (blinkCount >= 6) { // Blink 3 times
                        clearInterval(blinkInterval);
                        successIndicator.style.display = 'none';
                    }
                }, 300);
            } else {
                console.error('Server error:', data.error);
                alert('Error adding comment: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding comment. Please try again.');
        })
        .finally(() => {
            // Re-enable the submit button
            submitButton.disabled = false;
        });
    });
    
    // Modal functionality
    const modal = document.getElementById('editCommentModal');
    const closeBtn = document.querySelector('.close');
    const cancelBtn = document.querySelector('.cancel-edit');
    
    // Close modal when clicking on X
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking on cancel button
    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Function to open the edit modal
    function openEditModal() {
        const commentId = this.getAttribute('data-comment-id');
        const commentElement = document.getElementById('comment-' + commentId);
        const commentContent = commentElement.querySelector('.comment-content').textContent.trim();
        
        // Set the comment ID and content in the modal form
        document.getElementById('editCommentId').value = commentId;
        document.getElementById('editCommentContent').value = commentContent;
        
        // Show the modal
        modal.style.display = 'block';
    }
    
    // Add event listeners to all edit buttons
    document.querySelectorAll('.edit-comment-btn').forEach(button => {
        button.addEventListener('click', openEditModal);
    });
    
    // Handle edit comment form submission
    document.getElementById('editCommentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const commentId = document.getElementById('editCommentId').value;
        const content = document.getElementById('editCommentContent').value;
        
        // Create FormData and append the method field
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('content', content);
        
        // Get the submit button and disable it during submission
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        
        fetch('{{ url("/threads/" . $thread->id . "/comments") }}/' + commentId, {
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
                // Update the comment content in the DOM
                const commentElement = document.getElementById('comment-' + commentId);
                commentElement.querySelector('.comment-content').textContent = data.comment.content;
                
                // Close the modal
                modal.style.display = 'none';
                
            } else {
                console.error('Server error:', data.error);
                alert('Error updating comment: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating comment. Please try again.');
        })
        .finally(() => {
            // Re-enable the submit button
            submitButton.disabled = false;
        });
    });
</script>
@endsection