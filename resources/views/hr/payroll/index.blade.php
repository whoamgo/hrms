@extends('layouts.app')

@section('title', 'Payroll / Honorarium')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">Payroll / Honorarium</h4>
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
                                <option value="Honorarium">Honorarium</option>
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
            url: '{{ route("hr.payroll.data") }}',
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

        var payrollViewUrl = "{{ route('hr.payroll.show', ':id') }}";
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

    // Download PDF
    $(document).on('click', '.download-payslip-pdf', function() {
        var id = $(this).data('id');
        window.location.href = '{{ url("/hr/payroll") }}/' + id + '/pdf';
    });
});
</script>
@endpush


