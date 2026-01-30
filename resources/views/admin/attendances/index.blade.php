@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Attendance</h4>
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary">Add Attendance</a>
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
                            <label>Date</label>
                            <input type="date" class="form-control" id="filter_date" name="date">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Attendance</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="holiday">Holiday</option>
                                <option value="weekend">Weekend</option>
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
            <h5 class="mb-3">Attendance List</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="attendances-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Employee Name</th>
                            <th>Day</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Working Hours</th>
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
<div class="modal fade" id="viewAttendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewAttendanceModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteAttendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this attendance record?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAttendance">Delete</button>
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
    var table = $('#attendances-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.attendances.data") }}',
            data: function(d) {
                if ($('#filter_employee_id').val()) d.employee_id = $('#filter_employee_id').val();
                if ($('#filter_date').val()) d.date = $('#filter_date').val();
                if ($('#filter_status').val()) d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'date', name: 'date' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'day', name: 'day' },
            { data: 'check_in', name: 'check_in' },
            { data: 'check_out', name: 'check_out' },
            { data: 'working_hours', name: 'working_hours' },
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
                    url: '{{ route("admin.attendances.autocomplete.employee-name") }}',
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

    var deleteAttendanceId = null;

    $(document).on('click', '.view-attendance', function() {
        var attendanceId = $(this).data('id');

        var viewUrl = "{{ route('admin.attendances.show', ':id') }}";

        // Replace placeholder with real ID
        viewUrl = viewUrl.replace(':id', attendanceId);



        $('#viewAttendanceModal').modal('show');
        $('#viewAttendanceModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
        $.ajax({
            
            url: viewUrl,

            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                $('#viewAttendanceModalBody').html(response);
            },
            error: function(xhr) {
                $('#viewAttendanceModalBody').html('<div class="alert alert-danger">Error loading details.</div>');
            }
        });
    });

    $(document).on('click', '.delete-attendance', function() {
        deleteAttendanceId = $(this).data('id');

        $('#deleteAttendanceModal').modal('show');
    });

    $('#confirmDeleteAttendance').on('click', function() {
        if (deleteAttendanceId) {

            var deleteViewUrl = "{{ route('admin.attendances.show', ':id') }}";
            deleteViewUrl = deleteViewUrl.replace(':id', deleteAttendanceId);

            
            $.ajax({
                url: deleteViewUrl,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#deleteAttendanceModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        toastr.success(response.message);
                        //alert(response.message);
                    }
                    deleteAttendanceId = null;
                },
                error: function(xhr) {
                    $('#deleteAttendanceModal').modal('hide');
                    toastr.error('Error deleting attendance.');
                    //alert('');
                    deleteAttendanceId = null;
                }
            });
        }
    });
});
</script>
@endpush

