@extends('layouts.admin')

@section('admin-content')
<div class="card">
    <h1>All Users</h1>
    
    <table id="usersTable" class="display" style="width:100%">
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
                        <button class="btn btn-info btn-sm view-user" data-user-id="{{ $user->id }}">View</button>
                        <button class="btn btn-primary btn-sm edit-user" data-user-id="{{ $user->id }}">Edit</button>
                        @if(!$user->verified)
                            <form method="POST" action="{{ route('admin.generate-password', $user) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Generate Password</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- View User Modal -->
<div id="viewUserModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="card" style="position: relative; background-color: #fefefe; margin: 5% auto; padding: 0; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <div style="padding: 20px; border-bottom: 1px solid #eee; background: linear-gradient(to right, #FF6B00, #FF8C42); color: white; border-radius: 8px 8px 0 0;">
            <h2 style="margin: 0; display: inline-block;">User Details</h2>
            <span id="closeViewUserModal" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: white;">&times;</span>
        </div>
        
        <div id="userDetails" style="padding: 20px;">
            <!-- User details will be loaded here via AJAX -->
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="card" style="position: relative; background-color: #fefefe; margin: 5% auto; padding: 0; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <div style="padding: 20px; border-bottom: 1px solid #eee; background: linear-gradient(to right, #FF6B00, #FF8C42); color: white; border-radius: 8px 8px 0 0;">
            <h2 style="margin: 0; display: inline-block;">Edit User</h2>
            <span id="closeEditUserModal" style="float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: white;">&times;</span>
        </div>
        
        <div style="padding: 20px;">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="editUserName" style="display: block; margin-bottom: 5px; font-weight: bold;">Name</label>
                    <input type="text" name="name" id="editUserName" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="editUserShopName" style="display: block; margin-bottom: 5px; font-weight: bold;">Shop Name</label>
                    <input type="text" name="shop_name" id="editUserShopName" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="editUserMobile" style="display: block; margin-bottom: 5px; font-weight: bold;">Mobile No</label>
                    <input type="text" name="mobile_no" id="editUserMobile" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="editUserCity" style="display: block; margin-bottom: 5px; font-weight: bold;">City</label>
                    <input type="text" name="city" id="editUserCity" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="editUserAddress" style="display: block; margin-bottom: 5px; font-weight: bold;">Address</label>
                    <textarea name="address" id="editUserAddress" class="form-control" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: bold;">
                        <input type="checkbox" name="verified" id="editUserVerified" value="1" style="margin-right: 8px;"> Verified
                    </label>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" id="cancelEditUser" style="padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; background-color: #FF6B00; color: white; border: none; border-radius: 4px; cursor: pointer;">Update User</button>
                </div>
            </form>
            
            <div id="editUserMessage" style="margin-top: 15px;"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "order": [[ 1, "desc" ]], // Order by ID descending by default
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
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">ID:</strong>
                                <div style="margin-top: 5px;">${data.id}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Name:</strong>
                                <div style="margin-top: 5px;">${data.name}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Email:</strong>
                                <div style="margin-top: 5px;">${data.email}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Shop Name:</strong>
                                <div style="margin-top: 5px;">${data.shop_name || 'N/A'}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Mobile:</strong>
                                <div style="margin-top: 5px;">${data.mobile_no || 'N/A'}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">City:</strong>
                                <div style="margin-top: 5px;">${data.city || 'N/A'}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Address:</strong>
                                <div style="margin-top: 5px;">${data.address || 'N/A'}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00;">
                                <strong style="color: #555;">Verified:</strong>
                                <div style="margin-top: 5px;">${data.verified ? 'Yes' : 'No'}</div>
                            </div>
                            <div style="padding: 15px; background-color: #f9f9f9; border-radius: 6px; border-left: 4px solid #FF6B00; grid-column: span 2;">
                                <strong style="color: #555;">Created At:</strong>
                                <div style="margin-top: 5px;">${formattedDate}</div>
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
                        $('#editUserMessage').html('<div class="alert alert-success" style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">' + data.message + '</div>');
                        
                        // Update the table row with new data
                        var row = $('#usersTable').DataTable().row($('[data-user-id="' + userId + '"]').closest('tr'));
                        var rowData = row.data();
                        rowData[2] = data.user.name;
                        rowData[3] = data.user.shop_name || '';
                        rowData[4] = data.user.mobile_no || '';
                        rowData[5] = data.user.city || '';
                        rowData[6] = data.user.verified ? 'Yes' : 'No';
                        row.data(rowData).invalidate().draw();
                        
                        // Close modal after 1.5 seconds
                        setTimeout(function() {
                            $('#editUserModal').hide();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Error updating user.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#editUserMessage').html('<div class="alert alert-error" style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">' + errorMsg + '</div>');
                }
            });
        });
    });
</script>
@endsection