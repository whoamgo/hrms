@extends('layouts.app')

@section('title', 'Service Records')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Service Records</h4>
            <a href="{{ route('admin.service-records.create') }}" class="btn btn-primary">Add Service Record</a>
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
                            <label>Employee</label>
                            <input type="text" class="form-control" id="filter_employee_id" name="employee_id" placeholder="Search Employee">
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
                            <label>Designation</label>
                            <input type="text" class="form-control" id="filter_designation" name="designation" placeholder="Search Designation">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" id="filter_department" name="department" placeholder="Search Department">
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
                <table class="table table-striped table-bordered" id="service-records-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Period</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Remarks</th>
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
<div class="modal fade" id="viewServiceRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Service Record Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewServiceRecordModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteServiceRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this service record?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteServiceRecord">Delete</button>
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
    var table = $('#service-records-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.service-records.data") }}',
            data: function(d) {
                if ($('#filter_employee_id').val()) d.employee_id = $('#filter_employee_id').val();
                if ($('#filter_from_date').val()) d.from_date = $('#filter_from_date').val();
                if ($('#filter_to_date').val()) d.to_date = $('#filter_to_date').val();
                if ($('#filter_designation').val()) d.designation = $('#filter_designation').val();
                if ($('#filter_department').val()) d.department = $('#filter_department').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'period', name: 'period' },
            { data: 'designation', name: 'designation' },
            { data: 'department', name: 'department' },
            { data: 'remarks', name: 'remarks' },
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

    // Autocomplete for Designation
    var designationCache = {};
    $('#filter_designation').on('input', function() {
        var term = $(this).val();
        var $input = $(this);
        if (term.length < 1) {
            $input.parent().find('.autocomplete-suggestions').hide();
            return;
        }
        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (designationCache[term]) {
                showAutocomplete($input, designationCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("admin.service-records.autocomplete.designation") }}',
                    data: { term: term },
                    success: function(data) {
                        designationCache[term] = data;
                        showAutocomplete($input, data);
                    }
                });
            }
        }, 300));
    });

    // Autocomplete for Department
    var departmentCache = {};
    $('#filter_department').on('input', function() {
        var term = $(this).val();
        var $input = $(this);
        if (term.length < 1) {
            $input.parent().find('.autocomplete-suggestions').hide();
            return;
        }
        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (departmentCache[term]) {
                showAutocomplete($input, departmentCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("admin.service-records.autocomplete.department") }}',
                    data: { term: term },
                    success: function(data) {
                        departmentCache[term] = data;
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

    var deleteServiceRecordId = null;

    $(document).on('click', '.view-service-record', function() {
        var recordId = $(this).data('id');

        //alert(recordId)

        var viewURL = "{{ route('admin.service-records.show', ':id') }}";
        viewURL = viewURL.replace(':id', recordId);

        $('#viewServiceRecordModal').modal('show');
        $('#viewServiceRecordModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
        $.ajax({
            url: viewURL,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                $('#viewServiceRecordModalBody').html(response);
            },
            error: function(xhr) {
                console.log(xhr)
                var message = xhr.responseJSON?.error || 'Error loading employee details.';
                $('#viewEmployeeModalBody').html('<div class="alert alert-danger">' + message + '</div>');
            }
        });
    });

    $(document).on('click', '.delete-service-record', function() {
        deleteServiceRecordId = $(this).data('id');
        $('#deleteServiceRecordModal').modal('show');
    });

    $('#confirmDeleteServiceRecord').on('click', function() {
        if (deleteServiceRecordId) {

            var deleteViewUrl = "{{ route('admin.service-records.show', ':id') }}";
            deleteViewUrl = deleteViewUrl.replace(':id', deleteServiceRecordId);

            
            $.ajax({
                url: deleteViewUrl,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#deleteServiceRecordModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        alert(response.message);
                    }
                    deleteServiceRecordId = null;
                },
                error: function(xhr) {
                    $('#deleteServiceRecordModal').modal('hide');
                    alert('Error deleting service record.');
                    deleteServiceRecordId = null;
                }
            });
        }
    });
});
</script>
@endpush

