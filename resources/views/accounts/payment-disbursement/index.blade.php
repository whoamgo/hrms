@extends('layouts.app')

@section('title', 'Payment Disbursement')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Payment Disbursement</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createDisbursementModal">
                <i class="ti-plus"></i> Create Disbursement
            </button>
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
                            <label>Role</label>
                            <select class="form-control" id="filter_role" name="role">
                                <option value="">All</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Month</label>
                            <select class="form-control" id="filter_month" name="month">
                                <option value="">All</option>
                                <option value="January">January</option>
                                <option value="February">February</option>
                                <option value="March">March</option>
                                <option value="April">April</option>
                                <option value="May">May</option>
                                <option value="June">June</option>
                                <option value="July">July</option>
                                <option value="August">August</option>
                                <option value="September">September</option>
                                <option value="October">October</option>
                                <option value="November">November</option>
                                <option value="December">December</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" class="form-control" id="filter_year" name="year" value="{{ date('Y') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Disbursement Status</label>
                            <select class="form-control" id="filter_disbursement_status" name="disbursement_status">
                                <option value="">All</option>
                                <option value="Success">Success</option>
                                <option value="Pending">Pending</option>
                                <option value="Failed">Failed</option>
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
    <div class="col-lg-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="disbursement-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Employee Name</th>
                            <th>Bank Account</th>
                            <th>Amount</th>
                            <th>Transaction ID</th>
                            <th>Month/Year</th>
                            <th>Disbursement Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Disbursement Modal -->
<div class="modal fade" id="createDisbursementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Payment Disbursement</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-disbursement-form" data-ajax-form>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee Name <span class="text-danger">*</span></label>
                                <select class="form-control" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                </select>
                                <span class="text-danger error-text employee_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank Account Number</label>
                                <input type="text" class="form-control" id="bank_account_number" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>IFSC Code</label>
                                <input type="text" class="form-control" id="ifsc_code" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                <span class="text-danger error-text amount_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Transaction ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" required>
                                <span class="text-danger error-text transaction_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Month <span class="text-danger">*</span></label>
                                <select class="form-control" id="month" name="month" required>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                                <span class="text-danger error-text month_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="year" name="year" value="{{ date('Y') }}" required>
                                <span class="text-danger error-text year_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Disbursement Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="disbursement_status" name="disbursement_status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Success">Success</option>
                                    <option value="Failed">Failed</option>
                                </select>
                                <span class="text-danger error-text disbursement_status_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                <span class="text-danger error-text remarks_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            Submit
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
    var table = $('#disbursement-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("accounts.payment.data") }}',
            data: function(d) {
                d.role = $('#filter_role').val();
                d.month = $('#filter_month').val();
                d.year = $('#filter_year').val();
                d.disbursement_status = $('#filter_disbursement_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'bank_account', name: 'bank_account' },
            { data: 'amount', name: 'amount' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'month_year', name: 'month_year' },
            { 
                data: 'disbursement_status', 
                name: 'disbursement_status',
                render: function(data) {
                    if (data === 'Success') {
                        return '<span class="badge bg-success">Success</span>';
                    } else if (data === 'Failed') {
                        return '<span class="badge bg-danger">Failed</span>';
                    } else {
                        return '<span class="badge bg-warning">Pending</span>';
                    }
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#search-btn').on('click', function() {
        table.ajax.reload();
    });

    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        $('#filter_year').val('{{ date('Y') }}');
        table.ajax.reload();
    });

    // Load employees when role changes
    $('#role').on('change', function() {
        var roleSlug = $(this).val();
        var employeeSelect = $('#employee_id');
        employeeSelect.html('<option value="">Loading...</option>');
        
        if (roleSlug) {
            $.ajax({
                url: '{{ route("accounts.payment.employees-by-role") }}',
                type: 'GET',
                data: { role: roleSlug },
                success: function(response) {
                    if (response.success) {
                        employeeSelect.html('<option value="">Select Employee</option>');
                        response.employees.forEach(function(emp) {
                            employeeSelect.append('<option value="' + emp.id + '" data-bank-account="' + emp.bank_account_number + '" data-bank-name="' + emp.bank_name + '" data-ifsc="' + emp.ifsc_code + '">' + emp.name + '</option>');
                        });
                    }
                }
            });
        } else {
            employeeSelect.html('<option value="">Select Employee</option>');
        }
    });

    // Update bank info when employee is selected
    $('#employee_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        $('#bank_account_number').val(selectedOption.data('bank-account') || '');
        $('#bank_name').val(selectedOption.data('bank-name') || '');
        $('#ifsc_code').val(selectedOption.data('ifsc') || '');
    });

    // Create disbursement form
    $('#create-disbursement-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: '{{ route("accounts.payment.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#createDisbursementModal').modal('hide');
                    form[0].reset();
                    $('.error-text').text('');
                    $('#bank_account_number, #bank_name, #ifsc_code').val('');
                    table.ajax.reload();
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
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

    // View disbursement button click handler
    $(document).on('click', '.view-disbursement', function() {
        var disbursementId = $(this).data('id');
        
        $.ajax({
            url: '{{ route("accounts.payment.index") }}/' + disbursementId,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#viewDisbursementModal').remove();
                $('body').append(response);
                $('#viewDisbursementModal').modal('show');
            },
            error: function(xhr) {
                alert('Error loading disbursement details.');
            }
        });
    });
});
</script>
@endpush

