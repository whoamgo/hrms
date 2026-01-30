@extends('layouts.app')

@section('title', 'Monthly Payroll Processing')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Monthly Payroll Processing</h4>
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
                                <option value="Honorarium">Honorarium</option>
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
                            <th>Employee Name</th>
                            <th>Employee Type</th>
                            <th>Basic Pay / Honorarium</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
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
                                <label>Employee <span class="text-danger">*</span></label>
                                <select class="form-control" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee Type</label>
                                <select class="form-control" id="employee_type" name="employee_type">
                                    <option value="Permanent">Permanent</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Honorarium">Honorarium</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h5 class="input_top_hd">Earnings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Basic Pay / Honorarium <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="basic_salary" name="basic_salary" required>
                                <span class="text-danger error-text basic_salary_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Allowances</label>
                                <input type="number" step="0.01" class="form-control" id="allowances" name="allowances" value="0">
                                <span class="text-danger error-text allowances_error"></span>
                            </div>
                        </div>
                    </div>
                    <h5 class="input_top_hd">Deductions</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PF</label>
                                <input type="number" step="0.01" class="form-control" id="pf" name="pf" value="0">
                                <span class="text-danger error-text pf_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ESI</label>
                                <input type="number" step="0.01" class="form-control" id="esi" name="esi" value="0">
                                <span class="text-danger error-text esi_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TDS</label>
                                <input type="number" step="0.01" class="form-control" id="tds" name="tds" value="0">
                                <span class="text-danger error-text tds_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Deductions</label>
                                <input type="number" step="0.01" class="form-control" id="other_deductions" name="other_deductions" value="0">
                                <span class="text-danger error-text other_deductions_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Comp Off</label>
                                <input type="number" step="0.01" class="form-control" id="comp_off" name="comp_off" value="0">
                                <span class="text-danger error-text comp_off_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Days Payable <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="days_payable" name="days_payable" value="30" required>
                                <span class="text-danger error-text days_payable_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Net Pay (Auto Calculated)</label>
                                <input type="text" class="form-control" id="net_pay" readonly style="font-weight: bold; font-size: 18px;">
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
            url: '{{ route("accounts.payroll.data") }}',
            data: function(d) {
                d.role = $('#filter_role').val();
                var monthYear = $('#filter_month').val();
                if (monthYear) {
                    var parts = monthYear.split('-');
                    d.month = new Date(parts[0], parts[1] - 1).toLocaleString('default', { month: 'long' });
                    d.year = parts[0];
                }
                d.employee_type = $('#filter_employee_type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'month', name: 'month' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'employee_type', name: 'employee_type' },
            { data: 'basic_pay', name: 'basic_pay' },
            { data: 'allowances', name: 'allowances' },
            { data: 'deductions', name: 'deductions' },
            { data: 'net_pay', name: 'net_pay' },
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

    // Load employees when role changes
    $('#role').on('change', function() {
        var roleSlug = $(this).val();
        var employeeSelect = $('#employee_id');
        employeeSelect.html('<option value="">Loading...</option>');
        
        if (roleSlug) {
            $.ajax({
                url: '{{ route("accounts.payroll.employees-by-role") }}',
                type: 'GET',
                data: { role: roleSlug },
                success: function(response) {
                    if (response.success) {
                        employeeSelect.html('<option value="">Select Employee</option>');
                        response.employees.forEach(function(emp) {
                            employeeSelect.append('<option value="' + emp.id + '" data-type="' + emp.employee_type + '">' + emp.name + '</option>');
                        });
                    }
                }
            });
        } else {
            employeeSelect.html('<option value="">Select Employee</option>');
        }
    });

    // Update employee type when employee is selected
    $('#employee_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        $('#employee_type').val(selectedOption.data('type') || 'Permanent');
    });

    // Auto-calculate Net Pay
    function calculateNetPay() {
        var basicSalary = parseFloat($('#basic_salary').val()) || 0;
        var allowances = parseFloat($('#allowances').val()) || 0;
        var pf = parseFloat($('#pf').val()) || 0;
        var esi = parseFloat($('#esi').val()) || 0;
        var tds = parseFloat($('#tds').val()) || 0;
        var otherDeductions = parseFloat($('#other_deductions').val()) || 0;
        var compOff = parseFloat($('#comp_off').val()) || 0;
        
        var totalEarnings = basicSalary + allowances;
        var totalDeductions = pf + esi + tds + otherDeductions + compOff;
        var netPay = totalEarnings - totalDeductions;
        
        $('#net_pay').val('₹' + netPay.toFixed(2));
    }

    $('#basic_salary, #allowances, #pf, #esi, #tds, #other_deductions, #comp_off').on('input', calculateNetPay);
    
    // Calculate on page load if values exist
    calculateNetPay();

    // View Payslip
    $(document).on('click', '.view-payslip', function() {
        var id = $(this).data('id');

        var viewUrl = "{{ route('accounts.payroll.show', ':id') }}";
        viewUrl = viewUrl.replace(':id', id);

        
        $.ajax({
            url: viewUrl,
            type: 'GET',
            data: { ajax: true },
            success: function(response) {
                $('#viewPayslipBody').html(response);
                $('#viewPayslipModal').modal('show');
            }
        });
    });

    // Download PDF
    $(document).on('click', '.download-payslip-pdf', function() {
        var id = $(this).data('id');
        window.location.href = '/accounts/payroll/' + id + '/pdf';
    });

    // Generate Payslip Form
    $('#generate-payslip-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        // Add mobile_deduction as 0 if not present
        if (!formData.includes('mobile_deduction')) {
            formData += '&mobile_deduction=0';
        }
        
        $.ajax({
            url: '{{ route("accounts.payroll.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    //alert(response.message);
                    $('#generatePayslipModal').modal('hide');
                    form[0].reset();
                    $('.error-text').text('');
                    $('#net_pay').val('');
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

