@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title mb-0">{{ $thread->subject }}</h1>
    </div>
    <div class="card-body">
        <div class="thread-meta mb-3 text-muted">
            By {{ $thread->user->name }} | 
            {{ $thread->created_at->format('M d, Y H:i') }}
        </div>
        <div class="p-3 bg-light rounded mb-4">
            <p class="mb-0">{{ $thread->description }}</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Comments</h3>
    </div>
    <div class="card-body">
    
    <div id="commentsContainer">
        @foreach($thread->comments as $comment)
            <div class="comment" id="comment-{{ $comment->id }}">
                <div class="thread-meta text-muted">
                    By {{ $comment->user->name }} | 
                    {{ $comment->created_at->format('M d, Y H:i') }}
                    @auth
                        @if(Auth::id() === $comment->user_id)
                            <button class="btn btn-sm btn-primary edit-comment-btn" data-comment-id="{{ $comment->id }}" style="margin-left: 10px;">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        @endif
                    @endauth
                </div>
                <div class="comment-content mt-2">
                    {{ $comment->content }}
                    @if($comment->image_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $comment->image_path) }}" alt="Comment Image" class="img-fluid" style="max-width: 300px;">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    @auth
    <h4 class="mt-4">Add a Comment</h4>
    <form id="commentForm" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <textarea name="content" class="form-control" rows="4" placeholder="Write your comment here..." required></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="image" class="form-label">Attach an image (optional):</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>
        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Add Comment</button>
            <!-- Visual indicator for successful comment addition -->
            <span id="commentSuccessIndicator" class="ml-2 text-success fw-bold" style="display: none;">✓ Comment added successfully!</span>
        </div>
    </form>
    @else
    <div class="alert alert-info mt-4">
        <p class="mb-0">Please <a href="{{ route('login') }}">login</a> to add a comment.</p>
    </div>
    @endauth
    </div>
</div>

<!-- Edit Comment Modal -->
<div id="editCommentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Comment</h2>
            <button type="button" class="close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editCommentForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCommentId" name="comment_id">
                <div class="form-group">
                    <label for="editCommentContent" class="form-label">Comment:</label>
                    <textarea id="editCommentContent" name="content" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group mt-3">
                    <label for="editCommentImage" class="form-label">Update image (optional):</label>
                    <input type="file" name="image" id="editCommentImage" class="form-control" accept="image/*">
                    <div id="currentImageContainer" class="mt-2" style="display: none;">
                        <p class="mb-1">Current image:</p>
                        <img id="currentImage" src="" alt="Current Image" class="img-fluid" style="max-width: 200px;">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary mr-2 cancel-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle comment form submission
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('commentForm')) {
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
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </button>
                            </div>
                            <div class="comment-content mt-2">
                                ${data.comment.content}
                                ${data.comment.image_path ? '<div class="mt-2"><img src="/storage/' + data.comment.image_path + '" alt="Comment Image" class="img-fluid" style="max-width: 300px;"></div>' : ''}
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
        }
        
        // Modal functionality
        const modal = document.getElementById('editCommentModal');
        const closeBtn = document.querySelector('.close');
        const cancelBtn = document.querySelector('.cancel-edit');
        
        // Close modal when clicking on X
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (modal) modal.style.display = 'none';
            });
        }
        
        // Close modal when clicking on cancel button
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                if (modal) modal.style.display = 'none';
            });
        }
        
        // Close modal when clicking outside of it
        window.addEventListener('click', (event) => {
            if (modal && event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Function to open the edit modal
        function openEditModal() {
            const commentId = this.getAttribute('data-comment-id');
            const commentElement = document.getElementById('comment-' + commentId);
            const commentContent = commentElement.querySelector('.comment-content').textContent.trim();
            const commentImage = commentElement.querySelector('img');
            
            // Set the comment ID and content in the modal form
            document.getElementById('editCommentId').value = commentId;
            document.getElementById('editCommentContent').value = commentContent;
            
            // Show the modal
            if (modal) modal.style.display = 'block';
            
            // If there's an image, show it in the modal
            if (commentImage) {
                document.getElementById('currentImage').src = commentImage.src;
                document.getElementById('currentImageContainer').style.display = 'block';
            } else {
                document.getElementById('currentImageContainer').style.display = 'none';
            }
        }
        
        // Add event listeners to all edit buttons
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.addEventListener('click', openEditModal);
        });
        
        // Handle edit comment form submission
        if (document.getElementById('editCommentForm')) {
            document.getElementById('editCommentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const commentId = document.getElementById('editCommentId').value;
                const content = document.getElementById('editCommentContent').value;
                const imageFile = document.getElementById('editCommentImage').files[0];
                
                // Create FormData and append the method field
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('content', content);
                if (imageFile) {
                    formData.append('image', imageFile);
                }
                
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
                        const commentContentElement = commentElement.querySelector('.comment-content');
                        
                        // Update text content
                        commentContentElement.innerHTML = data.comment.content;
                        
                        // Handle image display
                        const existingImageContainer = commentContentElement.querySelector('.mt-2');
                        if (data.comment.image_path) {
                            // If there's a new image path, update or create image display
                            const imageUrl = '/storage/' + data.comment.image_path;
                            if (existingImageContainer) {
                                const imgElement = existingImageContainer.querySelector('img');
                                if (imgElement) {
                                    imgElement.src = imageUrl;
                                } else {
                                    existingImageContainer.innerHTML = `<img src="${imageUrl}" alt="Comment Image" class="img-fluid" style="max-width: 300px;">`;
                                }
                            } else {
                                commentContentElement.innerHTML += `<div class="mt-2"><img src="${imageUrl}" alt="Comment Image" class="img-fluid" style="max-width: 300px;"></div>`;
                            }
                        } else if (existingImageContainer) {
                            // If no image path and there was an image container, remove it
                            existingImageContainer.remove();
                        }
                        
                        // Close the modal
                        if (modal) modal.style.display = 'none';
                        
                        // Reset the image input
                        document.getElementById('editCommentImage').value = '';
                        
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
        }
    });
</script>
@endsection