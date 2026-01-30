@extends('layouts.app')

@section('title', 'Payroll / Honorarium')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Payroll / Honorarium</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generatePayslipModal">
                <i class="ti-plus"></i> Generate Payslip
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
                            <label>Month</label>
                            <input type="month" class="form-control" id="filter_month" name="month" value="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee Type</label>
                            <select class="form-control" id="filter_employee_type" name="employee_type">
                                <option value="">All</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contract">Contract</option>
                                <!-- <option value="Honorarium">Honorarium</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All</option>
                                <option value="Generated">Generated</option>
                                <option value="Not Generated">Not Generated</option>
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
                <table class="table table-striped table-bordered" id="payroll-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Month</th>
                            <th>Employee Type</th>
                            <th>Basic Pay / Honorarium</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
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

<!-- View Payslip Modal -->
<div class="modal fade" id="viewPayslipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payslip Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewPayslipBody">
                Loading...
            </div>
        </div>
    </div>
</div>

<!-- Generate Payslip Modal -->
<div class="modal fade" id="generatePayslipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Payslip</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="generate-payslip-form" data-ajax-form>
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee <span class="text-danger">*</span></label>
                                <select class="form-control" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_id }})</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text employee_id_error"></span>
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
                    </div>
                    <h5 class="input_top_hd">Earnings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Basic Salary <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="basic_salary" name="basic_salary" required>
                                <span class="text-danger error-text basic_salary_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>HRA</label>
                                <input type="number" step="0.01" class="form-control" id="hra" name="hra" value="0">
                                <span class="text-danger error-text hra_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conveyance Allowance</label>
                                <input type="number" step="0.01" class="form-control" id="conveyance_allowance" name="conveyance_allowance" value="0">
                                <span class="text-danger error-text conveyance_allowance_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Medical Allowance</label>
                                <input type="number" step="0.01" class="form-control" id="medical_allowance" name="medical_allowance" value="0">
                                <span class="text-danger error-text medical_allowance_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Special Allowance</label>
                                <input type="number" step="0.01" class="form-control" id="special_allowance" name="special_allowance" value="0">
                                <span class="text-danger error-text special_allowance_error"></span>
                            </div>
                        </div>
                    </div>
                    <h5 class="input_top_hd">Deductions</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ESI</label>
                                <input type="number" step="0.01" class="form-control" id="esi" name="esi" value="0">
                                <span class="text-danger error-text esi_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PF</label>
                                <input type="number" step="0.01" class="form-control" id="pf" name="pf" value="0">
                                <span class="text-danger error-text pf_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TDS</label>
                                <input type="number" step="0.01" class="form-control" id="tds" name="tds" value="0">
                                <span class="text-danger error-text tds_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Other Deductions</label>
                                <input type="number" step="0.01" class="form-control" id="other_deductions" name="other_deductions" value="0">
                                <span class="text-danger error-text other_deductions_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile Deduction</label>
                                <input type="number" step="0.01" class="form-control" id="mobile_deduction" name="mobile_deduction" value="0">
                                <span class="text-danger error-text mobile_deduction_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Comp Off</label>
                                <input type="number" step="0.01" class="form-control" id="comp_off" name="comp_off" value="0">
                                <span class="text-danger error-text comp_off_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Days Payable <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="days_payable" name="days_payable" value="30" required>
                                <span class="text-danger error-text days_payable_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            Generate Payslip
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payslip Modal -->
<div class="modal fade" id="editPayslipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payslip</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editPayslipBody">
                Loading...
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
    var table = $('#payroll-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.payroll.data") }}',
            data: function(d) {
                d.month = $('#filter_month').val();
                d.employee_type = $('#filter_employee_type').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'month', name: 'month' },
            { data: 'employee_type', name: 'employee_type' },
            { data: 'basic_pay', name: 'basic_pay' },
            { data: 'allowances', name: 'allowances' },
            { data: 'deductions', name: 'deductions' },
            { data: 'net_pay', name: 'net_pay' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    return '<span class="badge bg-success">' + data + '</span>';
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
        $('#filter_month').val('{{ date('Y-m') }}');
        table.ajax.reload();
    });

    // View Payslip
    $(document).on('click', '.view-payslip', function() {

        var id = $(this).data('id');
        var payrollViewUrl = "{{ route('admin.payroll.show', ':id') }}";
        payrollViewUrl = payrollViewUrl.replace(':id', id);

        $.ajax({
            url: payrollViewUrl,
            type: 'GET',
            data: { ajax: true },
            success: function(response) {
                $('#viewPayslipBody').html(response);
                $('#viewPayslipModal').modal('show');
            }
        });
    });


    var payrollEditUrl = "{{ route('admin.payroll.edit', ':id') }}";
    // Edit Payslip
    $(document).on('click', '.edit-payslip', function() {
        var id = $(this).data('id');
        window.location.href = payrollEditUrl.replace(':id', id);
    });


    var payrollPdfUrl = "{{ route('admin.payroll.pdf', ':id') }}";
    // Download PDF
    $(document).on('click', '.download-payslip-pdf', function () {
        var id = $(this).data('id');
        var url = payrollPdfUrl.replace(':id', id);

        window.location.href = url;
    });


    // Delete Payslip
    $(document).on('click', '.delete-payslip', function() {
        var id = $(this).data('id');

        var payrollViewUrl = "{{ route('admin.payroll.show', ':id') }}";
        payrollViewUrl = payrollViewUrl.replace(':id', id);


        if (confirm('Are you sure you want to delete this payslip ?')) {
            $.ajax({
                url: payrollViewUrl,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        //alert(response.message);
                        table.ajax.reload();
                    }
                }
            });
        }
    });

    // Generate Payslip Form
    $('#generate-payslip-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: '{{ route("admin.payroll.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    //alert(response.message);
                    $('#generatePayslipModal').modal('hide');
                    form[0].reset();
                    $('.error-text').text('');
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
});
</script>
@endpush


