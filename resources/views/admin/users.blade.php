@extends('layouts.admin')

@section('admin-content')
<div class="admin-card">
    <div class="admin-card-header">
        <h1 class="admin-card-title mb-0">All Users</h1>
    </div>
    <div class="admin-card-body">
        <table id="usersTable" class="admin-table display p-3" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Shop Name</th>
                    <th>Mobile</th>
                    <th>City</th>
                    <th>Verified</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $key => $user)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->shop_name }}</td>
                        <td>{{ $user->mobile_no }}</td>
                        <td>{{ $user->city }}</td>
                        <td>{{ $user->verified ? 'Yes' : 'No' }}</td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <button class="admin-btn admin-btn-info admin-btn-sm view-user" data-user-id="{{ $user->id }}"><i class="fas fa-eye"></i></button>
                            <button class="admin-btn admin-btn-primary admin-btn-sm edit-user" data-user-id="{{ $user->id }}"><i class="fas fa-pen"></i></button>
                            @if(!$user->verified)
                                <form method="POST" action="{{ route('admin.generate-password', $user) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn-success admin-btn-sm">Generate Password</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">User Details</h2>
            <button type="button" class="close" id="closeViewUserModal">&times;</button>
        </div>
        
        <div class="modal-body">
            <div id="userDetails">
                <!-- User details will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit User</h2>
            <button type="button" class="close" id="closeEditUserModal">&times;</button>
        </div>
        
        <div class="modal-body">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="admin-form-group">
                    <label for="editUserName" class="admin-form-label">Name</label>
                    <input type="text" name="name" id="editUserName" class="admin-form-control" required>
                </div>
                
                <div class="admin-form-group">
                    <label for="editUserShopName" class="admin-form-label">Shop Name</label>
                    <input type="text" name="shop_name" id="editUserShopName" class="admin-form-control">
                </div>
                
                <div class="admin-form-group">
                    <label for="editUserMobile" class="admin-form-label">Mobile No</label>
                    <input type="text" name="mobile_no" id="editUserMobile" class="admin-form-control">
                </div>
                
                <div class="admin-form-group">
                    <label for="editUserCity" class="admin-form-label">City</label>
                    <input type="text" name="city" id="editUserCity" class="admin-form-control">
                </div>
                
                <div class="admin-form-group">
                    <label for="editUserAddress" class="admin-form-label">Address</label>
                    <textarea name="address" id="editUserAddress" class="admin-form-control" rows="3"></textarea>
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">
                        <input type="checkbox" name="verified" id="editUserVerified" value="1"> Verified
                    </label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="admin-btn admin-btn-secondary mr-2" id="cancelEditUser">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Update User</button>
                </div>
            </form>
            
            <div id="editUserMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "order": [[ 0, "asc" ]], // Order by ID ascending by default
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
        
        // View User Modal
        $('.view-user').on('click', function() {
            var userId = $(this).data('user-id');
            
            $.ajax({
                url: '/admin/users/' + userId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var createdAt = new Date(data.created_at);
                    var formattedDate = createdAt.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    var userDetails = `
                        <div class="admin-user-details">
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">ID</div>
                                <div class="admin-user-detail-value">${data.id}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Name</div>
                                <div class="admin-user-detail-value">${data.name}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Email</div>
                                <div class="admin-user-detail-value">${data.email}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Shop Name</div>
                                <div class="admin-user-detail-value">${data.shop_name || 'N/A'}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Mobile</div>
                                <div class="admin-user-detail-value">${data.mobile_no || 'N/A'}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">City</div>
                                <div class="admin-user-detail-value">${data.city || 'N/A'}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Address</div>
                                <div class="admin-user-detail-value">${data.address || 'N/A'}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Verified</div>
                                <div class="admin-user-detail-value">${data.verified ? 'Yes' : 'No'}</div>
                            </div>
                            <div class="admin-user-detail-item">
                                <div class="admin-user-detail-label">Created At</div>
                                <div class="admin-user-detail-value">${formattedDate}</div>
                            </div>
                        </div>
                    `;
                    
                    $('#userDetails').html(userDetails);
                    $('#viewUserModal').show();
                },
                error: function() {
                    alert('Error fetching user details.');
                }
            });
        });
        
        // Edit User Modal
        $('.edit-user').on('click', function() {
            var userId = $(this).data('user-id');
            
            $.ajax({
                url: '/admin/users/' + userId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#editUserId').val(data.id);
                    $('#editUserName').val(data.name);
                    $('#editUserShopName').val(data.shop_name || '');
                    $('#editUserMobile').val(data.mobile_no || '');
                    $('#editUserCity').val(data.city || '');
                    $('#editUserAddress').val(data.address || '');
                    $('#editUserVerified').prop('checked', data.verified);
                    
                    $('#editUserMessage').html('');
                    $('#editUserModal').show();
                },
                error: function() {
                    alert('Error fetching user details for editing.');
                }
            });
        });
        
        // Close View Modal
        $('#closeViewUserModal').on('click', function() {
            $('#viewUserModal').hide();
        });
        
        // Close Edit Modal
        $('#closeEditUserModal, #cancelEditUser').on('click', function() {
            $('#editUserModal').hide();
        });
        
        // Close modals when clicking outside
        $(window).on('click', function(event) {
            if (event.target.id === 'viewUserModal') {
                $('#viewUserModal').hide();
            }
            if (event.target.id === 'editUserModal') {
                $('#editUserModal').hide();
            }
        });
        
        // Handle Edit User Form Submission
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            
            var userId = $('#editUserId').val();
            var formData = $(this).serialize();
            
            $.ajax({
                url: '/admin/users/' + userId,
                type: 'PUT',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        $('#editUserMessage').html('<div class="admin-alert admin-alert-success">' + data.message + '</div>');
                        
                        // Update the table row with new data
                        var row = $('#usersTable').DataTable().row($('[data-user-id="' + userId + '"]').closest('tr'));
                        var rowData = row.data();
                        rowData[1] = data.user.name;
                        rowData[2] = data.user.shop_name || '';
                        rowData[3] = data.user.mobile_no || '';
                        rowData[4] = data.user.city || '';
                        rowData[5] = data.user.verified ? 'Yes' : 'No';
                        row.data(rowData).invalidate().draw();
                        
                        // Close modal after 1.5 seconds
                        setTimeout(function() { w
                            $('#editUserModal').hide();
                        }, 1500);
                    } else {
                        $('#editUserMessage').html('<div class="admin-alert admin-alert-error">' + (data.message || 'Error updating user.') + '</div>');
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Error updating user.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#editUserMessage').html('<div class="admin-alert admin-alert-error">' + errorMsg + '</div>');
                }
            });
        });
    });
</script>
@endsection