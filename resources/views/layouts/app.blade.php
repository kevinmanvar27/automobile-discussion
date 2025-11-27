@extends('layouts.main')

@section('header-nav-items')
    <!-- New Thread button in header -->
    <a onclick="openThreadModal()" style="cursor: pointer;">New Thread</a>
@endsection

@section('modals')
    <!-- Thread Creation Modal (available globally) -->
    <div id="threadModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="card" style="position: relative; background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px;">
            <span onclick="closeThreadModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2>Create New Thread</h2>
            
            <form id="threadForm">
                @csrf
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Thread</button>
                <button type="button" class="btn btn-secondary" onclick="closeThreadModal()">Cancel</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Open the modal
        function openThreadModal() {
            document.getElementById('threadModal').style.display = 'block';
        }
        
        // Close the modal
        function closeThreadModal() {
            document.getElementById('threadModal').style.display = 'none';
        }
        
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('threadModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Handle form submission
        document.getElementById('threadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("threads.store") }}', {
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
    </script>
@endsection