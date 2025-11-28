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
            @if($thread->images->count() > 0)
                <div class="mt-3">
                    @foreach($thread->images as $image)
                        <div class="image-container d-inline-block position-relative mr-2 mb-2">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thread Image" class="img-fluid" style="max-width: 100px;">
                            @if(Auth::check() && Auth::id() === $thread->user_id)
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                        data-image-id="{{ $image->id }}" 
                                        data-image-type="thread"
                                        style="padding: 2px 5px; font-size: 0.7rem;">
                                    &times;
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Rating Section -->
        <div class="rating-container mb-4" data-thread-id="{{ $thread->id }}">
            <div class="star-rating-display">
                @php
                    $averageRating = round($thread->average_rating, 1);
                    $fullStars = floor($averageRating);
                    $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                @endphp
                
                <div class="star-rating-avg">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $fullStars)
                            <span class="star full">&#9733;</span>
                        @elseif ($i == $fullStars + 1 && $hasHalfStar)
                            <span class="star half">&#9733;</span>
                        @else
                            <span class="star empty">&#9733;</span>
                        @endif
                    @endfor
                </div>
                <div class="rating-info">
                    <span class="rating-value">{{ $averageRating }}</span>
                    <span class="rating-count">({{ $thread->ratings->count() }} {{ $thread->ratings->count() == 1 ? 'rating' : 'ratings' }})</span>
                </div>
            </div>
            
            @auth
            <div class="star-rating-input mt-2">
                <div class="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $thread->user_rating ? 'selected' : '' }}" data-rating="{{ $i }}">&#9733;</span>
                    @endfor
                </div>
                <div class="rating-text">{{ $thread->user_rating > 0 ? 'You rated: ' . $thread->user_rating . ' star' . ($thread->user_rating != 1 ? 's' : '') : 'Rate this thread' }}</div>
            </div>
            @endauth
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Comments</h3>
        <!-- Search Form -->
        <form method="GET" action="#" id="searchForm" class="me-2">
            <div class="input-group">
                <input type="text" name="search" id="searchInput" 
                    class="form-control"
                    placeholder="Search comments..." 
                    value="">
                <button type="button" id="resetSearchBtn" class="btn btn-primary">
                    <i id="searchIcon" class="fas fa-search"></i>
                </button>
            </div>
        </form>
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
                    @if($comment->images->count() > 0)
                        <div class="mt-2">
                            @foreach($comment->images as $image)
                                <div class="image-container d-inline-block position-relative mr-2 mb-2">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Comment Image" class="img-fluid" style="max-width: 100px;">
                                    @if(Auth::check() && Auth::id() === $comment->user_id)
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                                data-image-id="{{ $image->id }}" 
                                                data-image-type="comment"
                                                style="padding: 2px 5px; font-size: 0.7rem;">
                                            &times;
                                        </button>
                                    @endif
                                </div>
                            @endforeach
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
            <label for="images" class="form-label">Attach images (optional):</label>
            <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
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
                    <label for="editCommentImages" class="form-label">Add more images (optional):</label>
                    <input type="file" name="images[]" id="editCommentImages" class="form-control" accept="image/*" multiple>
                    <div id="currentImagesContainer" class="mt-2" style="display: none;">
                        <p class="mb-1">Current images:</p>
                        <div id="currentImages"></div>
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
    // Set global variables for JavaScript
    window.loginRoute = "{{ route('login') }}";
    
    // Handle comment form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Add search functionality
        const searchInput = document.getElementById('searchInput');
        const resetSearchBtn = document.getElementById('resetSearchBtn');
        const commentsContainer = document.getElementById('commentsContainer');
        
        // Function to filter comments based on search term
        function filterComments(searchTerm) {
            const comments = commentsContainer.querySelectorAll('.comment');
            
            comments.forEach(comment => {
                const commentText = comment.textContent.toLowerCase();
                const matchesSearch = commentText.includes(searchTerm.toLowerCase());
                
                if (searchTerm === '' || matchesSearch) {
                    comment.style.display = 'block';
                } else {
                    comment.style.display = 'none';
                }
            });
        }
        
        // Function to update search icon based on input value
        function updateSearchIcon(value) {
            const searchIcon = document.getElementById('searchIcon');
            if (searchIcon) {
                if (value && value.trim() !== '') {
                    searchIcon.className = 'fas fa-times';
                } else {
                    searchIcon.className = 'fas fa-search';
                }
            }
        }
        
        // Event listener for search input
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterComments(this.value);
                updateSearchIcon(this.value);
            });
        }
        
        // Event listener for reset button
        if (resetSearchBtn) {
            resetSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterComments('');
                updateSearchIcon('');
            });
        }
        
        // Update originalCommentsHTML when new comments are added
        function updateOriginalComments() {
            // Not needed anymore since we're filtering in real-time
        }
        
        // Expose the filter function for use in other parts of the code
        window.filterComments = filterComments;
        
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
                        
                        // Build image HTML if images exist
                        let imagesHtml = '';
                        if (data.comment.images && data.comment.images.length > 0) {
                            imagesHtml = '<div class="mt-2">';
                            data.comment.images.forEach(image => {
                                imagesHtml += `<div class="image-container d-inline-block position-relative mr-2 mb-2">
                                    <img src="/storage/${image.image_path}" alt="Comment Image" class="img-fluid" style="max-width: 100px;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                            data-image-id="${image.id}" 
                                            data-image-type="comment"
                                            style="padding: 2px 5px; font-size: 0.7rem;">
                                        &times;
                                    </button>
                                </div>`;
                            });
                            imagesHtml += '</div>';
                        }
                        
                        newComment.innerHTML = `
                            <div class="thread-meta">
                                By ${data.comment.user.name} | 
                                Just now
                                <button class="btn btn-sm btn-primary edit-comment-btn" data-comment-id="${data.comment.id}" style="margin-left: 10px;">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                            <div class="comment-content mt-2">
                                ${data.comment.content}
                                ${imagesHtml}
                            </div>
                        `;
                        commentsContainer.appendChild(newComment);
                        
                        // Add event listener to the new edit button
                        newComment.querySelector('.edit-comment-btn').addEventListener('click', openEditModal);
                        
                        // Add event listeners to delete image buttons
                        const deleteButtons = newComment.querySelectorAll('.delete-image-btn');
                        deleteButtons.forEach(button => {
                            button.addEventListener('click', handleDeleteImage);
                        });
                        
                        // Search functionality works in real-time, no need to update original HTML
                        
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
            const commentImages = commentElement.querySelectorAll('img');
            
            // Set the comment ID and content in the modal form
            document.getElementById('editCommentId').value = commentId;
            document.getElementById('editCommentContent').value = commentContent;
            
            // Show the modal
            if (modal) modal.style.display = 'block';
            
            // If there are images, show them in the modal
            if (commentImages.length > 0) {
                const currentImagesContainer = document.getElementById('currentImagesContainer');
                const currentImagesDiv = document.getElementById('currentImages');
                
                // Clear previous images
                currentImagesDiv.innerHTML = '';
                
                // Add all images
                commentImages.forEach(img => {
                    const imgClone = img.cloneNode();
                    imgClone.className = 'img-fluid mb-2';
                    imgClone.style.maxWidth = '150px';
                    currentImagesDiv.appendChild(imgClone);
                });
                
                currentImagesContainer.style.display = 'block';
            } else {
                document.getElementById('currentImagesContainer').style.display = 'none';
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
                const imageFiles = document.getElementById('editCommentImages').files;
                
                // Create FormData and append the method field
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('content', content);
                
                // Append all selected images
                for (let i = 0; i < imageFiles.length; i++) {
                    formData.append('images[]', imageFiles[i]);
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
                        if (data.comment.images && data.comment.images.length > 0) {
                            // If there are images, update or create image display
                            let imagesHtml = '<div class="mt-2">';
                            data.comment.images.forEach(image => {
                                imagesHtml += `<div class="image-container d-inline-block position-relative mr-2 mb-2">
                                    <img src="/storage/${image.image_path}" alt="Comment Image" class="img-fluid" style="max-width: 100px;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                            data-image-id="${image.id}" 
                                            data-image-type="comment"
                                            style="padding: 2px 5px; font-size: 0.7rem;">
                                        &times;
                                    </button>
                                </div>`;
                            });
                            imagesHtml += '</div>';
                            
                            if (existingImageContainer) {
                                existingImageContainer.outerHTML = imagesHtml;
                            } else {
                                commentContentElement.innerHTML += imagesHtml;
                            }
                        } else if (existingImageContainer) {
                            // If no images and there was an image container, remove it
                            existingImageContainer.remove();
                        }
                        
                        // Add event listeners to delete image buttons
                        const newDeleteButtons = commentElement.querySelectorAll('.delete-image-btn');
                        newDeleteButtons.forEach(button => {
                            button.addEventListener('click', handleDeleteImage);
                        });
                        
                        // Close the modal
                        if (modal) modal.style.display = 'none';
                        
                        // Reset the image input
                        document.getElementById('editCommentImages').value = '';
                        
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
        
        // Handle image deletion
        function handleDeleteImage() {
            const imageId = this.getAttribute('data-image-id');
            const imageType = this.getAttribute('data-image-type');
            
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            
            let url;
            if (imageType === 'thread') {
                url = `/thread-images/${imageId}`;
            } else if (imageType === 'comment') {
                url = `/comment-images/${imageId}`;
            } else {
                alert('Invalid image type');
                return;
            }
            
            fetch(url, {
                method: 'DELETE',
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
                    // Remove the image container from the DOM
                    this.closest('.image-container').remove();
                } else {
                    console.error('Server error:', data.error);
                    alert('Error deleting image: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting image. Please try again.');
            });
        }
        
        // Add event listeners to all delete image buttons
        document.querySelectorAll('.delete-image-btn').forEach(button => {
            button.addEventListener('click', handleDeleteImage);
        });
        
        // Initialize star ratings for the thread show page
        initializeStarRatings();
    });
    
    // Initialize star ratings
    function initializeStarRatings() {
        document.querySelectorAll('.star-rating').forEach(ratingContainer => {
            const stars = ratingContainer.querySelectorAll('.star');
            const threadId = ratingContainer.closest('.rating-container').dataset.threadId;
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(stars, rating);
                    
                    // Send rating to server
                    saveThreadRating(threadId, rating);
                    
                    // Update rating text
                    const ratingText = this.parentNode.nextElementSibling;
                    ratingText.textContent = `You rated: ${rating} star${rating !== 1 ? 's' : ''}`;
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(stars, rating, true);
                });
                
                star.addEventListener('mouseout', function() {
                    highlightStars(stars, getCurrentRating(stars));
                });
            });
        });
    }
    
    // Save thread rating
    function saveThreadRating(threadId, rating) {
        fetch(`/threads/${threadId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ rating: rating })
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = window.loginRoute;
                    return;
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update the average rating display
                const ratingContainer = document.querySelector(`.rating-container[data-thread-id="${threadId}"]`);
                if (ratingContainer) {
                    const avgRatingElement = ratingContainer.querySelector('.rating-value');
                    const ratingCountElement = ratingContainer.querySelector('.rating-count');
                    
                    if (avgRatingElement) {
                        avgRatingElement.textContent = parseFloat(data.average_rating).toFixed(1);
                    }
                    
                    if (ratingCountElement) {
                        const ratingCount = data.rating_count;
                        ratingCountElement.textContent = `(${ratingCount} ${ratingCount === 1 ? 'rating' : 'ratings'})`;
                    }
                    
                    // Update the star display for average rating
                    updateAverageStarDisplay(ratingContainer, parseFloat(data.average_rating));
                }
            } else {
                console.error('Server error:', data.error);
                alert('Error saving rating: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving rating. Please try again.');
        });
    }
    
    // Update the star display for average rating
    function updateAverageStarDisplay(ratingContainer, averageRating) {
        const starRatingAvg = ratingContainer.querySelector('.star-rating-avg');
        if (starRatingAvg) {
            const fullStars = Math.floor(averageRating);
            const hasHalfStar = (averageRating - fullStars) >= 0.5;
            
            starRatingAvg.innerHTML = '';
            
            for (let i = 1; i <= 5; i++) {
                let starClass = 'star';
                if (i <= fullStars) {
                    starClass += ' full';
                } else if (i == fullStars + 1 && hasHalfStar) {
                    starClass += ' half';
                } else {
                    starClass += ' empty';
                }
                
                const starSpan = document.createElement('span');
                starSpan.className = starClass;
                starSpan.innerHTML = '&#9733;';
                starRatingAvg.appendChild(starSpan);
            }
        }
    }
    
    // Get current rating from highlighted stars
    function getCurrentRating(stars) {
        for (let i = stars.length - 1; i >= 0; i--) {
            if (stars[i].classList.contains('selected')) {
                return i + 1;
            }
        }
        return 0;
    }
    
    // Highlight stars up to the given rating
    function highlightStars(stars, rating, isHover = false) {
        stars.forEach((star, index) => {
            if (index < rating) {
                if (isHover) {
                    star.classList.add('active');
                    star.classList.remove('selected');
                } else {
                    star.classList.add('selected');
                    star.classList.remove('active');
                }
            } else {
                star.classList.remove('active', 'selected');
            }
        });
    }
</script>
@endsection