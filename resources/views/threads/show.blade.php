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
    <h2>Comments</h2>
    
    <div id="commentsContainer">
        @foreach($thread->comments as $comment)
            <div class="comment">
                <div class="thread-meta">
                    By {{ $comment->user->name }} | 
                    {{ $comment->created_at->format('M d, Y H:i') }}
                </div>
                <div style="margin-top: var(--spacing-sm);">
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
                newComment.innerHTML = `
                    <div class="thread-meta">
                        By ${data.comment.user.name} | 
                        Just now
                    </div>
                    <div style="margin-top: var(--spacing-sm);">
                        ${data.comment.content}
                    </div>
                `;
                commentsContainer.appendChild(newComment);
                
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
</script>
@endsection