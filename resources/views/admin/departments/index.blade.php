@extends('layouts.app')

@section('title', 'Department Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Department Management</h4>
            <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#departmentModal" id="addDepartmentBtn">
                <i class="mdi mdi-plus"></i> Add Department
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="departments-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalTitle">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="department-form" data-ajax-form>
                    <input type="hidden" id="department_id" name="id">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="department_name" name="name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="department_description" name="description" rows="3"></textarea>
                        <span class="text-danger error-text description_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            Save
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
    var table = $('#departments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.departments.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { 
                data: 'is_active', 
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    // Add Department
    $('#addDepartmentBtn').on('click', function() {
        $('#departmentModalTitle').text('Add Department');
        $('#department-form')[0].reset();
        $('#department_id').val('');
        $('.error-text').text('');
    });

    // Edit Department
    $(document).on('click', '.edit-department', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/departments/' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#departmentModalTitle').text('Edit Department');
                    $('#department_id').val(response.data.id);
                    $('#department_name').val(response.data.name);
                    $('#department_description').val(response.data.description);
                    $('#departmentModal').modal('show');
                }
            }
        });
    });

    // Delete Department
    $(document).on('click', '.delete-department', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this department?')) {
            $.ajax({
                url: '/admin/departments/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        table.ajax.reload();
                    }
                }
            });
        }
    });

    // Toggle Status
    $(document).on('click', '.toggle-department-status', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/departments/' + id + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                }
            }
        });
    });

    // Form submission
    $('#department-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var departmentId = $('#department_id').val();
        var url = departmentId ? '/admin/departments/' + departmentId : '/admin/departments';
        var method = departmentId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#departmentModal').modal('hide');
                    form[0].reset();
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key.replace('.', '_') + '_error').text(value[0]);
                    });
                }
            }
        });
    });
});
</script>
@endpush

