@extends('layouts.app')

@section('title', 'Leave Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Leave Management</h4>
            <a href="{{ route('admin.leaves.create') }}" class="btn btn-primary">Add Leave</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="filter-form">
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee Name</label>
                            <input type="text" class="form-control" id="filter_employee_id" name="employee_id" placeholder="Search Employee Name">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Leave Type</label>
                            <select class="form-control" id="filter_leave_type" name="leave_type">
                                <option value="">All Types</option>
                                @foreach($leaveTypes as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" class="form-control" id="filter_from_date" name="from_date">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" class="form-control" id="filter_to_date" name="to_date">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
                            <button type="button" class="btn btn-secondary" id="reset-filters">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="leaves-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Employee Name</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
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

<!-- View Modal -->
<div class="modal fade" id="viewLeaveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewLeaveModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteLeaveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this leave record?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteLeave">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Leave</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejection_reason" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectLeave">Reject</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/datatable.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ced4da;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    display: none;
}
.autocomplete-suggestion {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}
.autocomplete-suggestion:hover {
    background-color: #f8f9fa;
}
.form-group {
    position: relative;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#leaves-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.leaves.data") }}',
            data: function(d) {
                if ($('#filter_employee_id').val()) d.employee_id = $('#filter_employee_id').val();
                if ($('#filter_leave_type').val()) d.leave_type = $('#filter_leave_type').val();
                if ($('#filter_from_date').val()) d.from_date = $('#filter_from_date').val();
                if ($('#filter_to_date').val()) d.to_date = $('#filter_to_date').val();
                if ($('#filter_status').val()) d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'leave_type', name: 'leave_type' },
            { data: 'dates', name: 'dates' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    if (data === 'approved') {
                        return '<span class="badge badge-success">Approved</span>';
                    } else if (data === 'rejected') {
                        return '<span class="badge badge-danger">Rejected</span>';
                    } else {
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#search-btn').on('click', function() {
        table.draw();
    });

    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        table.draw();
    });

    // Autocomplete for Employee Name
    var nameCache = {};
    $('#filter_employee_id').on('input', function() {
        var term = $(this).val();
        var $input = $(this);
        if (term.length < 1) {
            $input.parent().find('.autocomplete-suggestions').hide();
            return;
        }
        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (nameCache[term]) {
                showAutocomplete($input, nameCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("admin.leaves.autocomplete.employee-name") }}',
                    data: { term: term },
                    success: function(data) {
                        nameCache[term] = data;
                        showAutocomplete($input, data);
                    }
                });
            }
        }, 300));
    });

    function showAutocomplete($input, suggestions) {
        var $container = $input.parent();
        var $suggestions = $container.find('.autocomplete-suggestions');
        if ($suggestions.length === 0) {
            $suggestions = $('<div class="autocomplete-suggestions"></div>');
            $container.css('position', 'relative').append($suggestions);
        }
        if (suggestions.length === 0) {
            $suggestions.hide();
            return;
        }
        var html = '';
        suggestions.forEach(function(item) {
            html += '<div class="autocomplete-suggestion" data-value="' + item + '">' + item + '</div>';
        });
        $suggestions.html(html).show();
        $suggestions.find('.autocomplete-suggestion').on('click', function() {
            $input.val($(this).data('value'));
            $suggestions.hide();
            table.draw();
        });
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.form-group').length) {
            $('.autocomplete-suggestions').hide();
        }
    });

    var deleteLeaveId = null;
    var rejectLeaveId = null;

    $(document).on('click', '.view-leave', function() {
        var leaveId = $(this).data('id');
        $('#viewLeaveModal').modal('show');
        $('#viewLeaveModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
        $.ajax({
            url: '{{ url("/admin/leaves") }}/' + leaveId,
            type: 'GET',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/json'
            },
            success: function(response) {
                if (typeof response === 'string') {
                    $('#viewLeaveModalBody').html(response);
                } else if (response.error) {
                    $('#viewLeaveModalBody').html('<div class="alert alert-danger">' + response.error + '</div>');
                } else {
                    $('#viewLeaveModalBody').html('<div class="alert alert-danger">Error loading details.</div>');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Error loading details.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    try {
                        var error = JSON.parse(xhr.responseText);
                        if (error.error) {
                            errorMessage = error.error;
                        }
                    } catch(e) {
                        // If response is HTML, show generic error
                    }
                }
                $('#viewLeaveModalBody').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    });

    $(document).on('click', '.approve-leave', function() {
        var leaveId = $(this).data('id');

        var url = "{{ route('admin.leaves.approve', ':id') }}";   
        var url = url.replace(':id', leaveId);

        if (confirm('Are you sure you want to approve this leave?')) {
            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        table.draw();
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error approving leave.');
                }
            });
        }
    });

    $(document).on('click', '.reject-leave', function() {
        rejectLeaveId = $(this).data('id');
        $('#rejection_reason').val('');
        $('#rejectLeaveModal').modal('show');
    });

    $('#confirmRejectLeave').on('click', function() {
       // alert("ok")
        if (rejectLeaveId && $('#rejection_reason').val()) {
             var rejectLeaveUrl = "{{ route('admin.leaves.reject', ':id') }}";   
             var url = rejectLeaveUrl.replace(':id', rejectLeaveId);

            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { rejection_reason: $('#rejection_reason').val() },
                success: function(response) {
                    $('#rejectLeaveModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        alert(response.message);
                    }
                    rejectLeaveId = null;
                },
                error: function(xhr) {
                    var message = xhr.responseJSON?.message || 'Error rejecting leave.';
                    alert(message);
                    rejectLeaveId = null;
                }
            });
        } else {
            alert('Please provide a rejection reason.');
        }
    });

    $(document).on('click', '.delete-leave', function() {
        deleteLeaveId = $(this).data('id');
        $('#deleteLeaveModal').modal('show');
    });

    $('#confirmDeleteLeave').on('click', function() {
        if (deleteLeaveId) {

            var deleteUrl = "{{ route('admin.leaves.show', ':id') }}";
            deleteUrl = deleteUrl.replace(':id', deleteLeaveId);



            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#deleteLeaveModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        alert(response.message);
                    }
                    deleteLeaveId = null;
                },
                error: function(xhr) {
                    $('#deleteLeaveModal').modal('hide');
                    alert('Error deleting leave.');
                    deleteLeaveId = null;
                }
            });
        }
    });
});
</script>
@endpush

