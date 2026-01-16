@extends('layouts.app')

@section('title', 'Designation Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Designation Management</h4>
            <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#designationModal" id="addDesignationBtn">
                <i class="mdi mdi-plus"></i> Add Designation
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="designations-table" style="width:100%">
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

<!-- Designation Modal -->
<div class="modal fade" id="designationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="designationModalTitle">Add Designation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="designation-form" data-ajax-form>
                    <input type="hidden" id="designation_id" name="id">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="designation_name" name="name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="designation_description" name="description" rows="3"></textarea>
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
    var table = $('#designations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.designations.data") }}',
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

    // Add Designation
    $('#addDesignationBtn').on('click', function() {
        $('#designationModalTitle').text('Add Designation');
        $('#designation-form')[0].reset();
        $('#designation_id').val('');
        $('.error-text').text('');
    });

    // Edit Designation
    $(document).on('click', '.edit-designation', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/designations/' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#designationModalTitle').text('Edit Designation');
                    $('#designation_id').val(response.data.id);
                    $('#designation_name').val(response.data.name);
                    $('#designation_description').val(response.data.description);
                    $('#designationModal').modal('show');
                }
            }
        });
    });

    // Delete Designation
    $(document).on('click', '.delete-designation', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this designation?')) {
            $.ajax({
                url: '/admin/designations/' + id,
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
    $(document).on('click', '.toggle-designation-status', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/designations/' + id + '/toggle-status',
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
    $('#designation-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var designationId = $('#designation_id').val();
        var url = designationId ? '/admin/designations/' + designationId : '/admin/designations';
        var method = designationId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#designationModal').modal('hide');
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

