@extends('layouts.app')

@section('title', 'View Payslips')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">View Payslips</h4>
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
                            <label>Select Month</label>
                            <select class="form-control" id="filter_month" name="month">
                                <option value="1" {{ $month == 1 ? 'selected' : '' }}>January</option>
                                <option value="2" {{ $month == 2 ? 'selected' : '' }}>February</option>
                                <option value="3" {{ $month == 3 ? 'selected' : '' }}>March</option>
                                <option value="4" {{ $month == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ $month == 5 ? 'selected' : '' }}>May</option>
                                <option value="6" {{ $month == 6 ? 'selected' : '' }}>June</option>
                                <option value="7" {{ $month == 7 ? 'selected' : '' }}>July</option>
                                <option value="8" {{ $month == 8 ? 'selected' : '' }}>August</option>
                                <option value="9" {{ $month == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ $month == 10 ? 'selected' : '' }}>October</option>
                                <option value="11" {{ $month == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ $month == 12 ? 'selected' : '' }}>December</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Select Year</label>
                            <select class="form-control" id="filter_year" name="year">
                                @for($y = date('Y'); $y >= 2012; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
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
            <h5 class="mb-3" id="payslip-month-year"></h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="payslips-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Present Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
$(document).ready(function() {
     var payslipShowUrl = "{{ route('employee.payslips.show', ':id') }}";
    var table = $('#payslips-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("employee.payslips.data") }}',
            data: function(d) {
                d.month = $('#filter_month').val();
                d.year = $('#filter_year').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'present_days', name: 'present_days' },
            { 
                data: 'id', 
                name: 'action',
                render: function (data, type, row) {
                    var url = payslipShowUrl.replace(':id', data);
                    return '<a href="' + url + '" class="link_a">Salary Slip</a>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#search-btn').on('click', function() {
        var month = $('#filter_month option:selected').text();
        var year = $('#filter_year').val();
        $('#payslip-month-year').text(month + ', ' + year);
        table.ajax.reload();
    });

    // Load initial data
    $('#search-btn').click();
});
</script>
@endpush


