@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Reports</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="report-form">
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                            <span class="text-danger error-text from_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="to_date" name="to_date" required>
                            <span class="text-danger error-text to_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Report Types <span class="text-danger">*</span></label>
                            <select class="form-control" id="report_type" name="report_type" required>
                                <option value="">Select Report Types</option>
                                <option value="Employee List">Employee List</option>
                                <option value="Leave Report">Leave Report</option>
                                <option value="Payroll Report">Payroll Report</option>
                            </select>
                            <span class="text-danger error-text report_type_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee Type</label>
                            <select class="form-control" id="employee_type" name="employee_type">
                                <option value="">All</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control" id="department" name="department">
                                <option value="">All</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row" id="report-results" style="display: none;">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="d-flex align-items-center mb-2 justify-content-between">
                <h5 class="mb-3" id="report-title">Report</h5>
                <div class="d-flex">
                    <button type="button" class="btn btn-dropbox waves-effect waves-light flexbtn" id="export-pdf-btn">
                        <span class="btn-label"><i class="mdi mdi-file-pdf"></i></span>Export PDF
                    </button>
                   <!--  <button type="button" class="btn btn-dropbox waves-effect waves-light flexbtn" id="export-excel-btn">
                        <span class="btn-label"><i class="mdi mdi-file-excel-outline"></i></span>Export Excel
                    </button> -->
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="report-table" style="width:100%">
                    <thead class="thead-light" id="report-thead">
                    </thead>
                    <tbody id="report-tbody">
                    </tbody>
                </table>
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
var reportData = null;
var reportType = null;

$('#report-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    var spinner = submitBtn.find('.spinner-border');
    
    spinner.show();
    submitBtn.prop('disabled', true);

    $.ajax({
        url: '{{ route("hr.reports.generate") }}',
        type: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                reportData = response.data;
                reportType = response.report_type;
                displayReport(response.data, response.report_type);
                $('#report-results').show();
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Display validation errors
                $.each(xhr.responseJSON.errors, function(key, value) {
                    $('.' + key.replace('.', '_') + '_error').text(value[0]);
                });
            } else {
                alert('Error generating report: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        },
        complete: function() {
            spinner.hide();
            submitBtn.prop('disabled', false);
        }
    });
});

function displayReport(data, type) {
    var thead = $('#report-thead');
    var tbody = $('#report-tbody');
    var title = $('#report-title');
    
    title.text(type);
    thead.empty();
    tbody.empty();

    if (type === 'Leave Report') {
        thead.html('<tr><th>#</th><th>Employee Name</th><th>Employee Type</th><th>Department</th><th>Leave Type</th><th>From Date</th><th>To Date</th><th>Total Days</th><th>Reason</th><th>Status</th></tr>');
        data.forEach(function(item, index) {
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + (item.employee ? item.employee.full_name : 'N/A') + '</td>' +
                '<td>' + (item.employee ? item.employee.employee_type : 'N/A') + '</td>' +
                '<td>' + (item.employee ? item.employee.department : 'N/A') + '</td>' +
                '<td>' + (item.leave_type == 'CL' ? 'Casual Leave' : (item.leave_type == 'SL' ? 'Sick Leave' : 'Special Leave')) + '</td>' +
                '<td>' + new Date(item.from_date).toLocaleDateString('en-GB') + '</td>' +
                '<td>' + new Date(item.to_date).toLocaleDateString('en-GB') + '</td>' +
                '<td>' + item.total_days + '</td>' +
                '<td>' + (item.reason || 'N/A') + '</td>' +
                '<td><span class="badge bg-' + (item.status == 'approved' ? 'success' : (item.status == 'rejected' ? 'danger' : 'warning')) + '">' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>' +
                '</tr>';
            tbody.append(row);
        });
    } else if (type === 'Employee List') {
        thead.html('<tr><th>#</th><th>Employee ID</th><th>Name</th><th>Employee Type</th><th>Department</th><th>Designation</th><th>Status</th></tr>');
        data.forEach(function(item, index) {
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + item.employee_id + '</td>' +
                '<td>' + item.full_name + '</td>' +
                '<td>' + item.employee_type + '</td>' +
                '<td>' + (item.department || 'N/A') + '</td>' +
                '<td>' + (item.designation || 'N/A') + '</td>' +
                '<td><span class="badge bg-' + (item.status == 'active' ? 'success' : 'danger') + '">' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>' +
                '</tr>';
            tbody.append(row);
        });
    } else if (type === 'Payroll Report') {
        thead.html('<tr><th>#</th><th>Employee Name</th><th>Month</th><th>Basic Salary</th><th>Total Earnings</th><th>Total Deductions</th><th>Net Pay</th></tr>');
        data.forEach(function(item, index) {
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + (item.employee ? item.employee.full_name : 'N/A') + '</td>' +
                '<td>' + item.month + ' ' + item.year + '</td>' +
                '<td>₹' + parseFloat(item.basic_salary).toFixed(2) + '</td>' +
                '<td>₹' + parseFloat(item.total_earnings).toFixed(2) + '</td>' +
                '<td>₹' + parseFloat(item.total_deductions).toFixed(2) + '</td>' +
                '<td>₹' + parseFloat(item.salary_payable).toFixed(2) + '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }
}

$('#export-pdf-btn').on('click', function() {
    if (!reportData) {
        alert('Please generate a report first.');
        return;
    }
    
    $.ajax({
        url: '{{ route("hr.reports.export-pdf") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            data: JSON.stringify(reportData),
            report_type: reportType
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob) {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report-' + reportType.toLowerCase().replace(' ', '-') + '-' + new Date().toISOString().split('T')[0] + '.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    });
});

$('#export-excel-btn').on('click', function() {
    if (!reportData) {
        alert('Please generate a report first.');
        return;
    }
    
    $.ajax({
        url: '{{ route("hr.reports.export-excel") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            data: JSON.stringify(reportData),
            report_type: reportType
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob) {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report-' + reportType.toLowerCase().replace(' ', '-') + '-' + new Date().toISOString().split('T')[0] + '.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        },
        error: function(xhr) {
            alert('Error exporting Excel: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
    });
});
</script>
@endpush


