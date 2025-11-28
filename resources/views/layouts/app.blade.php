@extends('layouts.main')

@section('header-nav-items')
    <!-- New Thread button in header -->
    <a onclick="openThreadModal()" class="btn btn-primary btn-sm">New Thread</a>
@endsection

@section('modals')
    <!-- Thread Creation Modal (available globally) -->
    <div id="threadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Thread</h2>
                <button type="button" class="close" onclick="closeThreadModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="threadForm">
                    @csrf
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary mr-2" onclick="closeThreadModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Thread</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Open the modal
        function openThreadModal() {
            if (document.getElementById('threadModal')) {
                document.getElementById('threadModal').style.display = 'block';
            }
        }
        
        // Close the modal
        function closeThreadModal() {
            if (document.getElementById('threadModal')) {
                document.getElementById('threadModal').style.display = 'none';
            }
        }
        
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('threadModal');
            if (modal && event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Handle form submission
        document.addEventListener('DOMContentLoaded', function() {
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