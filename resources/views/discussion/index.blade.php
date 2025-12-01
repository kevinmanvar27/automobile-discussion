@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        
        <!-- LEFT: Title -->
        <h1 class="card-title mb-0">Discussion</h1>

        <!-- RIGHT: Search + New Thread -->
        <div class="d-flex align-items-center p-0">
            <!-- Search Form -->
            <form method="GET" action="{{ route('discussion.index') }}" id="searchForm" class="me-2">
                <div class="input-group">
                    <input type="text" name="search" id="searchInput" 
                        class="form-control"
                        placeholder="Search threads..." 
                        value="{{ request('search') }}">
                    <button type="button" id="resetSearchBtn" class="btn btn-primary">
                        <i id="searchIcon" class="{{ request('search') ? 'fas fa-times' : 'fas fa-search' }}"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
    
    @if($threads->count() > 0)
        <ul class="thread-list">
            @foreach($threads as $thread)
                <li class="thread-item d-flex justify-content-between align-items-center" id="thread-{{ $thread->id }}">
                    <div class="col-8 thread-left">
                        <a href="{{ route('threads.show', $thread) }}" class="thread-title">{{ $thread->subject }}</a>
                        @if($thread->images->count() > 0)
                            <div class="thread-images mt-2">
                                @foreach($thread->images->take(3) as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thread Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px;">
                                @endforeach
                                @if($thread->images->count() > 3)
                                    <span class="badge bg-secondary">+{{ $thread->images->count() - 3 }} more</span>
                                @endif
                            </div>
                        @endif
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
                    </div>
                    <div class="col-2 thread-center">
                        <div class="rating-container" data-thread-id="{{ $thread->id }}">
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
                                    <?php
                                        $currentUserId = Auth::id();
                                        $rating = $thread->ratings->where('user_id', $currentUserId)->value('rating');
                                        
                                    ?>
                                    <div class="star-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $rating && $i <= $rating ? 'selected' : '' }}"
                                                data-rating="{{ $i }}">
                                                &#9733;
                                            </span>
                                        @endfor
                                    </div>
                                    <div class="rating-text">{{ isset($thread->user_rating) && $thread->user_rating > 0 ? 'You rated: ' . $thread->user_rating . ' star' . ($thread->user_rating != 1 ? 's' : '') : 'Rate this thread' }}</div>
                                </div>
                            @endauth
                        </div>
                    </div>
                    <div class="col-2 thread-right">
                        ({{ $thread->comments_count }} {{ $thread->comments_count == 1 ? 'comment' : 'comments' }})
                    </div>
                </li>
            @endforeach
        </ul>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $threads->links() }}
        </div>
    @else
        <div class="text-center py-5" id="noThreadsMessage">
            <h3>No threads found</h3>
            <p class="mb-4">
                @if(request('search'))
                    No threads matched your search "{{ request('search') }}". 
                    <a href="{{ route('discussion.index') }}">View all threads</a>
                @else
                    Be the first to start a discussion!
                @endif
            </p>
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
            <form id="editThreadForm" enctype="multipart/form-data">
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
                
                <!-- Current Images Section -->
                <div class="form-group" id="currentThreadImagesContainer" style="display: none;">
                    <label class="form-label">Current Images:</label>
                    <div id="currentThreadImages" class="d-flex flex-wrap gap-2 mb-3"></div>
                </div>
                
                <div class="form-group">
                    <label for="editThreadImages" class="form-label">Add more images (optional):</label>
                    <input type="file" name="images[]" id="editThreadImages" class="form-control" accept="image/*" multiple>
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
    // Set global variables for JavaScript
    window.loginRoute = "{{ route('login') }}";
    
    // Ensure the modal functions are available
    if (typeof openThreadModal !== 'function') {
        // Fallback in case the global functions aren't available
        function openThreadModal() {
            document.getElementById('threadModal').style.display = 'block';
        }
    }
    
    // Hide Laravel pagination on page load if there's a search query
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const laravelPagination = document.querySelector('.d-flex.justify-content-center.mt-4');
        
        if (searchInput && searchInput.value && laravelPagination) {
            laravelPagination.style.display = 'none';
        }
    });
    
    // Function to reset search
    function resetSearch() {
        // Clear the search input
        document.getElementById('searchInput').value = '';   
        
        // Change icon back to search
        const searchIcon = document.getElementById('searchIcon');
        if (searchIcon) {
            searchIcon.className = 'fas fa-search';
        }
        
        // Show Laravel pagination again
        const laravelPagination = document.querySelector('.d-flex.justify-content-center.mt-4');
        if (laravelPagination) {
            laravelPagination.style.display = 'flex'; // Restore original display property
        }
        
        // Remove AJAX pagination if it exists
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }
             
        // Submit the form to reload with no search query
        document.getElementById('searchForm').submit();
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
                    if (response.status === 401) {
                        window.location.href = window.loginRoute;
                        return;
                    }
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Set the thread ID and content in the modal form
                document.getElementById('editThreadId').value = data.id;
                document.getElementById('editThreadSubject').value = data.subject;
                document.getElementById('editThreadDescription').value = data.description;
                
                // Handle existing images
                const currentImagesContainer = document.getElementById('currentThreadImagesContainer');
                const currentImagesDiv = document.getElementById('currentThreadImages');
                
                // Clear previous images
                currentImagesDiv.innerHTML = '';
                
                // Check if thread has images
                if (data.images && data.images.length > 0) {
                    // Add all images
                    data.images.forEach(image => {
                        const img = document.createElement('div');
                        img.className = 'position-relative';
                        img.innerHTML = `
                            <img src="/storage/${image.image_path}" alt="Thread Image" class="img-fluid" style="max-width: 150px;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" 
                                    data-image-id="${image.id}" 
                                    data-image-type="thread"
                                    style="padding: 2px 5px; font-size: 0.7rem;">
                                &times;
                            </button>
                        `;
                        currentImagesDiv.appendChild(img);
                    });
                    
                    currentImagesContainer.style.display = 'block';
                    
                    // Add event listeners to delete image buttons
                    currentImagesDiv.querySelectorAll('.delete-image-btn').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const imageId = this.getAttribute('data-image-id');
                            const imageType = this.getAttribute('data-image-type');
                            
                            if (confirm('Are you sure you want to delete this image?')) {
                                fetch(`/thread-images/${imageId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        // Remove the image element
                                        this.parentElement.remove();
                                        
                                        // Hide container if no images left
                                        if (currentImagesDiv.children.length === 0) {
                                            currentImagesContainer.style.display = 'none';
                                        }
                                    } else {
                                        alert('Error deleting image: ' + (result.error || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error deleting image. Please try again.');
                                });
                            }
                        });
                    });
                } else {
                    currentImagesContainer.style.display = 'none';
                }
                
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
        // Initialize star ratings
        initializeStarRatings();
        
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
                // Clear current images when closing modal
                document.getElementById('currentThreadImages').innerHTML = '';
                document.getElementById('currentThreadImagesContainer').style.display = 'none';
                // Reset file input
                document.getElementById('editThreadImages').value = '';
            });
        }
        
        if (document.getElementById('cancelEditThread')) {
            document.getElementById('cancelEditThread').addEventListener('click', function() {
                document.getElementById('editThreadModal').style.display = 'none';
                // Clear current images when closing modal
                document.getElementById('currentThreadImages').innerHTML = '';
                document.getElementById('currentThreadImagesContainer').style.display = 'none';
                // Reset file input
                document.getElementById('editThreadImages').value = '';
            });
        }
        
        // Close the modal when clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editThreadModal');
            if (event.target == modal) {
                modal.style.display = 'none';
                // Clear current images when closing modal
                document.getElementById('currentThreadImages').innerHTML = '';
                document.getElementById('currentThreadImagesContainer').style.display = 'none';
                // Reset file input
                document.getElementById('editThreadImages').value = '';
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
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'  // Laravel method spoofing
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            window.location.href = window.loginRoute;
                            return;
                        }
                        if (response.status === 403) {
                            alert('You are not authorized to edit this thread.');
                            document.getElementById('editThreadModal').style.display = 'none';
                            return;
                        }
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
                        
                        // Update images in the thread list
                        const threadImagesContainer = threadElement.querySelector('.thread-images');
                        if (threadImagesContainer) {
                            threadImagesContainer.remove();
                        }
                        
                        // Add updated images if any
                        if (data.thread.images && data.thread.images.length > 0) {
                            let imagesHtml = '<div class="thread-images mt-2">';
                            const imagesToShow = data.thread.images.slice(0, 3);
                            imagesToShow.forEach(image => {
                                imagesHtml += `<img src="/storage/${image.image_path}" alt="Thread Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px;">`;
                            });
                            if (data.thread.images.length > 3) {
                                imagesHtml += `<span class="badge bg-secondary">+${data.thread.images.length - 3} more</span>`;
                            }
                            imagesHtml += '</div>';
                            
                            // Insert after the thread title
                            threadTitleElement.insertAdjacentHTML('afterend', imagesHtml);
                        }
                        
                        // Close the modal
                        document.getElementById('editThreadModal').style.display = 'none';
                        
                        // Clear current images when closing modal
                        document.getElementById('currentThreadImages').innerHTML = '';
                        document.getElementById('currentThreadImagesContainer').style.display = 'none';
                        // Reset file input
                        document.getElementById('editThreadImages').value = '';
                        
                        // Show success message
                        alert('Thread updated successfully!');
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
        
        // Live search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            
            // Always show reset button and set initial icon
            const resetBtn = document.getElementById('resetSearchBtn');
            const searchIcon = document.getElementById('searchIcon');
            
            // Set initial icon based on whether there's a search value
            if (searchIcon) {
                searchIcon.className = searchInput.value ? 'fas fa-times' : 'fas fa-search';
            }
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                // Change icon based on input value
                if (searchIcon) {
                    searchIcon.className = this.value ? 'fas fa-times' : 'fas fa-search';
                }
                
                // Search if the query is at least 2 characters long, or if it's been cleared (0 characters)
                if (this.value.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        performSearch(this.value, 1); // Reset to first page for new search
                    }, 500); // Debounce for 500ms
                } else {
                    // If search is cleared or has fewer than 2 characters, show all threads
                    searchTimeout = setTimeout(() => {
                        performSearch('', 1); // Reset to first page
                    }, 500); // Debounce for 500ms
                }
            });
        }
        
        // Add event listener to reset button
        const resetBtn = document.getElementById('resetSearchBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetSearch);
        }
    });
    
    // Function to perform search via AJAX
    function performSearch(query, page = 1) {
        fetch(`{{ route('discussion.index') }}?search=${encodeURIComponent(query)}&page=${page}&ajax=1`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateThreadList(data.threads);
            updatePagination(data.pagination);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }
    
    // Function to update pagination controls
    function updatePagination(paginationData) {
        // If no pagination data, hide pagination
        if (!paginationData) {
            const paginationContainer = document.querySelector('.pagination-container');
            if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }
            return;
        }
        
        // Create pagination HTML
        let paginationHtml = '<nav aria-label="Thread pagination"><ul class="pagination justify-content-center">';
        
        // Previous button
        if (paginationData.current_page > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page - 1}">Previous</a></li>`;
        } else {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        // Page numbers (show up to 5 pages around current page)
        const startPage = Math.max(1, paginationData.current_page - 2);
        const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === paginationData.current_page) {
                paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
        }
        
        // Next button
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${paginationData.current_page + 1}">Next</a></li>`;
        } else {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        paginationHtml += '</ul></nav>';
        
        // Update or create pagination container
        let paginationContainer = document.querySelector('.pagination-container');
        if (!paginationContainer) {
            const cardBody = document.querySelector('.card-body');
            paginationContainer = document.createElement('div');
            paginationContainer.className = 'pagination-container mt-4';
            cardBody.appendChild(paginationContainer);
        }
        
        paginationContainer.innerHTML = paginationHtml;
        
        // Add event listeners to pagination links
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page) {
                    const searchQuery = document.getElementById('searchInput').value;
                    performSearch(searchQuery, page);
                }
            });
        });
    }
    
    // Function to update the thread list dynamically
    function updateThreadList(threads) {
        const threadList = document.querySelector('.thread-list');
        const noThreadsMessage = document.getElementById('noThreadsMessage');
        const paginationContainer = document.querySelector('.pagination-container');
        const laravelPagination = document.querySelector('.d-flex.justify-content-center.mt-4'); // Laravel pagination div
        
        if (threads.length > 0) {
            // Hide the "no threads" message
            if (noThreadsMessage) {
                noThreadsMessage.style.display = 'none';
            }
            
            // Create HTML for threads
            let threadsHtml = '';
            threads.forEach(thread => {
                // Create images HTML if thread has images
                let threadImagesHtml = '';
                if (thread.images && thread.images.length > 0) {
                    threadImagesHtml = '<div class="thread-images mt-2">';
                    const imagesToShow = thread.images.slice(0, 3);
                    imagesToShow.forEach(image => {
                        threadImagesHtml += `<img src="/storage/${image.image_path}" alt="Thread Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; margin-right: 5px;">`;
                    });
                    if (thread.images.length > 3) {
                        threadImagesHtml += `<span class="badge bg-secondary">+${thread.images.length - 3} more</span>`;
                    }
                    threadImagesHtml += '</div>';
                }
                
                // Check if current user can edit this thread
                const userCanEdit = thread.user_id == document.querySelector('meta[name="user-id"]')?.getAttribute('content');
                
                // Format date
                const threadCreatedAt = new Date(thread.created_at);
                const threadFormattedDate = threadCreatedAt.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                // Create comment count text
                const threadCommentText = `${thread.comments_count} ${thread.comments_count === 1 ? 'comment' : 'comments'}`;
                
                // Calculate average rating
                const averageRating = parseFloat(thread.average_rating) || 0;
                const roundedRating = Math.round(averageRating * 10) / 10; // Round to 1 decimal place
                const fullStars = Math.floor(roundedRating);
                const hasHalfStar = (roundedRating - fullStars) >= 0.5;
                const ratingCount = thread.ratings_count || 0;
                
                // Create star rating display
                let starRatingHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= fullStars) {
                        starRatingHtml += '<span class="star full">&#9733;</span>';
                    } else if (i == fullStars + 1 && hasHalfStar) {
                        starRatingHtml += '<span class="star half">&#9733;</span>';
                    } else {
                        starRatingHtml += '<span class="star empty">&#9733;</span>';
                    }
                }
                
                // Create user rating input (if authenticated)
                let userRatingHtml = '';
                const isAuthenticated = !!document.querySelector('meta[name="user-id"]');
                if (isAuthenticated) {
                    const userRating = thread.user_rating || 0;
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        starsHtml += `<span class="star ${userRating && i <= userRating ? 'selected' : ''}" data-rating="${i}">&#9733;</span>`;
                    }
                    const ratingText = userRating > 0 ? `You rated: ${userRating} star${userRating !== 1 ? 's' : ''}` : 'Rate this thread';
                    
                    userRatingHtml = `
                        <div class="star-rating-input mt-2">
                            <div class="star-rating">
                                ${starsHtml}
                            </div>
                            <div class="rating-text">${ratingText}</div>
                        </div>
                    `;
                }
                
                threadsHtml += `
                    <li class="thread-item d-flex justify-content-between align-items-center" id="thread-${thread.id}">
                        <div class="col-8 thread-left">
                            <a href="/threads/${thread.id}" class="thread-title">${thread.subject}</a>
                            ${threadImagesHtml}
                            <div class="thread-meta">
                                By ${thread.user.name} | 
                                ${threadFormattedDate}
                                ${userCanEdit ? 
                                    `<button class="btn btn-sm btn btn-primary edit-thread-btn" data-thread-id="${thread.id}" style="margin-left: 10px;">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>` : ''}
                            </div>
                        </div>
                        <div class="col-2 thread-center">
                            <div class="rating-container" data-thread-id="${thread.id}">
                                <div class="star-rating-display">
                                    <div class="star-rating-avg">
                                        ${starRatingHtml}
                                    </div>
                                    <div class="rating-info">
                                        <span class="rating-value">${roundedRating.toFixed(1)}</span>
                                        <span class="rating-count">(${ratingCount} ${ratingCount === 1 ? 'rating' : 'ratings'})</span>
                                    </div>
                                </div>
                                ${userRatingHtml}
                            </div>
                        </div>
                        <div class="col-2 thread-right">
                            (${threadCommentText})
                        </div>
                    </li>
                `;
            });
            
            // Update the thread list
            if (threadList) {
                threadList.innerHTML = threadsHtml;
                
                // Reattach event listeners to edit buttons
                document.querySelectorAll('.edit-thread-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openEditThreadModal.call(this);
                    });
                });
                
                // Reinitialize star ratings for dynamically loaded threads
                initializeStarRatings();
            }
            
            // Hide Laravel pagination when using AJAX
            if (laravelPagination) {
                laravelPagination.style.display = 'none';
            }
        } else {
            // Show "no threads" message
            if (noThreadsMessage) {
                noThreadsMessage.style.display = 'block';
                // Update the message based on whether there's a search term
                const searchTerm = document.getElementById('searchInput').value;
                if (searchTerm) {
                    noThreadsMessage.querySelector('h3').textContent = 'No threads found';
                    noThreadsMessage.querySelector('p').innerHTML = 
                        `No threads matched your search "${searchTerm}". 
                        <a href="{{ route('discussion.index') }}">View all threads</a>`;
                } else {
                    noThreadsMessage.querySelector('h3').textContent = 'No threads yet';
                    noThreadsMessage.querySelector('p').textContent = 'Be the first to start a discussion!';
                }
            } else {
                // If noThreadsMessage doesn't exist in DOM, create it
                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    // Check if user is authenticated
                    const isAuthenticated = !!document.querySelector('meta[name="user-id"]');
                    
                    // Create appropriate message based on authentication status
                    let authButton = '';
                    if (isAuthenticated) {
                        authButton = '<button class="btn btn-primary" onclick="openThreadModal()">Create the first thread</button>';
                    } else {
                        authButton = '<a href="'+window.loginRoute+'" class="btn btn-primary">Login to create a thread</a>';
                    }
                    
                    // Clear existing content and add the no threads message
                    cardBody.innerHTML = `
                        <div class="text-center py-5" id="noThreadsMessage">
                            <h3>No threads found</h3>
                            <p class="mb-4">
                                No threads matched your search.
                                <a href="{{ route('discussion.index') }}">View all threads</a>
                            </p>
                            ${authButton}
                        </div>
                    `;
                }
            }
            
            // Clear the thread list if it exists
            if (threadList) {
                threadList.innerHTML = '';
            }
            
            // Hide pagination when no threads
            if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }
            
            // Hide Laravel pagination when no threads
            if (laravelPagination) {
                laravelPagination.style.display = 'none';
            }
        }
    }
    
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
                    // Reset to current rating
                    const currentRating = getCurrentRating(stars);
                    if (currentRating > 0) {
                        highlightStars(stars, currentRating);
                    } else {
                        // Remove all highlights if no current rating
                        stars.forEach(s => s.classList.remove('active', 'selected'));
                    }
                });
            });
        });
    }
    
    // Save thread rating to server
    function saveThreadRating(threadId, rating) {
        fetch(`/threads/${threadId}/rate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                rating: rating
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Rating saved successfully');
                // Update the average rating display
                updateAverageRatingDisplay(threadId, data.average_rating, data.rating_count);
            } else {
                console.error('Error saving rating:', data.error);
                alert('Error saving rating: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving rating. Please try again.');
        });
    }
    
    // Update average rating display
    function updateAverageRatingDisplay(threadId, averageRating, ratingCount) {
        const ratingContainer = document.querySelector(`.rating-container[data-thread-id="${threadId}"]`);
        if (ratingContainer) {
            const ratingValue = ratingContainer.querySelector('.rating-value');
            const ratingCountElement = ratingContainer.querySelector('.rating-count');
            
            if (ratingValue) {
                ratingValue.textContent = parseFloat(averageRating).toFixed(1);
            }
            
            if (ratingCountElement) {
                ratingCountElement.textContent = `(${ratingCount} ${ratingCount === 1 ? 'rating' : 'ratings'})`;
            }
            
            // Update the star display for average rating
            updateAverageStarDisplay(ratingContainer, averageRating);
        }
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
    
    function highlightStars(stars, rating, isHover = false) {
        stars.forEach((star, index) => {

            // HOVER MODE
            if (isHover) {
                // Remove only hover highlight
                star.classList.remove('active');

                // Add active only to hovered range
                if (index < rating) {
                    star.classList.add('active');
                }
                return; // prevent touching selected class
            }

            // CLICK MODE (real selection)
            if (index < rating) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }

            // clean active from previous hover
            star.classList.remove('active');
        });
    }

</script>
@endsection