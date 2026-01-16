@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">User Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="header-title">Users</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-3" style="display:none;">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_role">Filter by Role</label>
                            <select class="form-control" id="filter_role" name="role_id">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_status">Filter by Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-secondary btn-block" id="reset-filters">
                                <i class="mdi mdi-refresh"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTable will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewUserModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user "<strong id="deleteUserName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/datatable.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.users.data") }}',
            data: function(d) {
                d.role_id = $('#filter_role').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    if (data === 'active') {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                }
            },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    // Apply filters
    $('#filter_role, #filter_status').on('change', function() {
        table.draw();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        $('#filter_role, #filter_status').val('');
        table.draw();
    });

    var deleteUserId = null;

    // View user modal
    $(document).on('click', '.view-user', function() {
        var userId = $(this).data('id');

        var ViewURL = "{{ route('admin.users.show', ':id') }}";
        ViewURL = ViewURL.replace(':id', userId);


        $('#viewUserModal').modal('show');
        $('#viewUserModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading user details...</p></div>');
        
        $.ajax({
            url: ViewURL,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#viewUserModalBody').html(response);
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.error || 'Error loading user details.';
                $('#viewUserModalBody').html('<div class="alert alert-danger">' + message + '</div>');
            }
        });
    });

    // Delete user modal
    $(document).on('click', '.delete-user', function() {
        deleteUserId = $(this).data('id');
        var userName = $(this).data('name');
        $('#deleteUserName').text(userName);
        $('#deleteUserModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteUser').on('click', function() {
        if (deleteUserId) {
            $.ajax({
                url: '/admin/users/' + deleteUserId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteUserModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        // Show success message
                        var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>';
                        $('.container-fluid').prepend(alertHtml);
                        setTimeout(function() {
                            $('.alert').fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    } else {
                        alert(response.message);
                    }
                    deleteUserId = null;
                },
                error: function(xhr) {
                    $('#deleteUserModal').modal('hide');
                    var message = xhr.responseJSON?.message || 'Error deleting user.';
                    alert(message);
                    deleteUserId = null;
                }
            });
        }
    });
});
</script>
@endpush

