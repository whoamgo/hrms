@extends('layouts.app')

@section('title', 'Employee Master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Employee Master</h4>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">Add Employee</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <!-- Search/Filter Form -->
            <form id="filter-form">
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input type="text" class="form-control" id="filter_employee_id" name="employee_id" placeholder="Search Employee ID">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="filter_name" name="name" placeholder="Search Name">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee Type</label>
                            <select class="form-control" id="filter_employee_type" name="employee_type">
                                <option value="">All Types</option>
                                @foreach($employeeTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
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
                            <label>Role</label>
                            <select class="form-control" id="filter_role" name="role_id">
                                <option value="">All Roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
                <table class="table table-striped table-bordered" id="employees-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Employee Type</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
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

<!-- View Employee Modal -->
<div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEmployeeModalLabel">Employee Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewEmployeeModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading employee details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEmployeeModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete employee "<strong id="deleteEmployeeName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteEmployee">Yes, Delete</button>
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

.autocomplete-suggestion:last-child {
    border-bottom: none;
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
    // Initialize DataTable - shows all records by default
    var table = $('#employees-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("hr.employees.data") }}',
            data: function(d) {
                // Only send filter values if they are not empty
                var employeeId = $('#filter_employee_id').val();
                var name = $('#filter_name').val();
                var employeeType = $('#filter_employee_type').val();
                var department = $('#filter_department').val();
                var roleId = $('#filter_role').val();
                var status = $('#filter_status').val();
                
                // Only add to data if value exists
                if (employeeId) d.employee_id = employeeId;
                if (name) d.name = name;
                if (employeeType) d.employee_type = employeeType;
                if (department) d.department = department;
                if (roleId) d.role_id = roleId;
                if (status) d.status = status;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'name', name: 'name' },
            { data: 'employee_type', name: 'employee_type' },
            { data: 'department', name: 'department' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    var checked = data === 'active' ? 'checked' : '';
                    return '<label class="switch custom_switch">' +
                           '<input type="checkbox" class="status-toggle" data-id="' + row.route_key + '" ' + checked + '>' +
                           '<span class="slider round"></span>' +
                           '</label>';
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    // Search button
    $('#search-btn').on('click', function() {
        table.draw();
    });

    // Reset filters
    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        table.draw();
    });

    // Enter key in filter inputs
    $('#filter-form input, #filter-form select').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            table.draw();
        }
    });

    // Autocomplete for Employee ID
    var employeeIdCache = {};
    $('#filter_employee_id').on('input', function() {
        var term = $(this).val();
        var $input = $(this);
        var $container = $input.parent();
        var $suggestions = $container.find('.autocomplete-suggestions');
        
        if (term.length < 1) {
            $suggestions.hide();
            return;
        }

        // Debounce the request
        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (employeeIdCache[term]) {
                showAutocomplete($input, employeeIdCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("hr.employees.autocomplete.employee-id") }}',
                    data: { term: term },
                    success: function(data) {
                        employeeIdCache[term] = data;
                        showAutocomplete($input, data);
                    }
                });
            }
        }, 300));
    });

    // Autocomplete for Name
    var nameCache = {};
    $('#filter_name').on('input', function() {
        var term = $(this).val();
        var $input = $(this);
        var $container = $input.parent();
        var $suggestions = $container.find('.autocomplete-suggestions');
        
        if (term.length < 1) {
            $suggestions.hide();
            return;
        }

        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (nameCache[term]) {
                showAutocomplete($input, nameCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("hr.employees.autocomplete.name") }}',
                    data: { term: term },
                    success: function(data) {
                        nameCache[term] = data;
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
        var $container = $input.parent();
        var $suggestions = $container.find('.autocomplete-suggestions');
        
        if (term.length < 1) {
            $suggestions.hide();
            return;
        }

        clearTimeout($input.data('timeout'));
        $input.data('timeout', setTimeout(function() {
            if (departmentCache[term]) {
                showAutocomplete($input, departmentCache[term]);
            } else {
                $.ajax({
                    url: '{{ route("hr.employees.autocomplete.department") }}',
                    data: { term: term },
                    success: function(data) {
                        departmentCache[term] = data;
                        showAutocomplete($input, data);
                    }
                });
            }
        }, 300));
    });

    // Simple autocomplete function
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

        // Handle suggestion click
        $suggestions.find('.autocomplete-suggestion').on('click', function() {
            $input.val($(this).data('value'));
            $suggestions.hide();
            table.draw();
        });
    }

    // Hide suggestions when clicking outside or when input loses focus
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.form-group').length) {
            $('.autocomplete-suggestions').hide();
        }
    });

    // Hide suggestions when input loses focus (with delay to allow click on suggestion)
    $('#filter_employee_id, #filter_name, #filter_department').on('blur', function() {
        var $input = $(this);
        setTimeout(function() {
            $input.parent().find('.autocomplete-suggestions').hide();
        }, 200);
    });

    var deleteEmployeeId = null;

    // View employee modal
    $(document).on('click', '.view-employee', function() {
        var employeeId = $(this).data('id');

        var employeeViewUrl = "{{ route('hr.employees.show', ':id') }}";
        employeeViewUrl = employeeViewUrl.replace(':id', employeeId);


        $('#viewEmployeeModal').modal('show');
        $('#viewEmployeeModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading employee details...</p></div>');
        
        $.ajax({
            url: employeeViewUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#viewEmployeeModalBody').html(response);
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.error || 'Error loading employee details.';
                $('#viewEmployeeModalBody').html('<div class="alert alert-danger">' + message + '</div>');
            }
        });
    });

    // Delete employee modal
    $(document).on('click', '.delete-employee', function() {
        deleteEmployeeId = $(this).data('id');
        var employeeName = $(this).data('name');
        $('#deleteEmployeeName').text(employeeName);
        $('#deleteEmployeeModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteEmployee').on('click', function() {
        if (deleteEmployeeId) {
            $.ajax({
                url: '/hr/employees/' + deleteEmployeeId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteEmployeeModal').modal('hide');
                    if (response.success) {
                        table.draw();
                        var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>';
                        $('.container-fluid').prepend(alertHtml);
                        setTimeout(function() {
                            $('.alert').fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    } else {
                        toastr.error(response.message);
                        //alert(response.message);
                    }
                    deleteEmployeeId = null;
                },
                error: function(xhr) {
                    $('#deleteEmployeeModal').modal('hide');
                    var message = xhr.responseJSON?.message || 'Error deleting employee.';
                    //alert(message);
                    toastr.error(message);
                    deleteEmployeeId = null;
                }
            });
        }
    });

    // Toggle status
    $(document).on('change', '.status-toggle', function() {
        var employeeId = $(this).data('id');



        var employeeViewUrl = "{{ route('hr.employees.toggle-status', ':id') }}";
        employeeViewUrl = employeeViewUrl.replace(':id', employeeId);




        var isChecked = $(this).is(':checked');
        var status = isChecked ? 'active' : 'inactive';
        
        $.ajax({
           // url: '/hr/employees/' + employeeId + '/toggle-status',
            url:employeeViewUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Status updated successfully
                } else {
                    toastr.error(response.message);
                    // Revert toggle
                    $('.status-toggle[data-id="' + employeeId + '"]').prop('checked', !isChecked);
                }
            },
            error: function(xhr) {
                 var message = xhr.responseJSON?.message || 'Error updating status.';
                // alert(message);
                toastr.success(message);
                // Revert toggle
                $('.status-toggle[data-id="' + employeeId + '"]').prop('checked', !isChecked);
            }
        });
    });
});
</script>
@endpush

