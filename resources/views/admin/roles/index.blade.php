@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Role Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="header-title">Roles</h4>
                    </div>
                   <!--  <div class="col-md-6 text-right">
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Add New Role
                        </a>
                    </div> -->
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="roles-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                               <!--  <th>Permissions</th>
                                <th>Menu Items</th> -->
                               <th>Status</th>  
                                <!-- <th>Actions</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->slug }}</td>
                                <td>{{ $role->description }}</td>
                               <!--  <td>
                                    <span class="badge badge-info">{{ $role->permissions->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $role->menuItems->count() }}</span>
                                </td> -->
                                <td>
                                    @if($role->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td style="display: none;">
                                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                   <!--  <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-role" data-id="{{ $role->id }}">
                                        <i class="mdi mdi-delete"></i>
                                    </button> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete role
    $(document).on('click', '.delete-role', function() {
        var roleId = $(this).data('id');
        if (confirm('Are you sure you want to delete this role?')) {
            $.ajax({
                url: '/admin/roles/' + roleId,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error deleting role.');
                }
            });
        }
    });
});
</script>
@endpush

