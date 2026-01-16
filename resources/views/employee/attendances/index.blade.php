@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Attendance</h4>
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

                    <div class="col-xl-12 col-md-12">
                        <div class="button_attendance">
                            <div class="button-list">
                                <button type="button" class="btn btn-secondary waves-effect waves-light">Weekend</button>
                                <button type="button" class="btn btn-success waves-effect">Festival</button>
                                <button type="button" class="btn btn-purple waves-effect waves-light">Leave</button>
                                <button type="button" class="btn btn-info waves-effect waves-light">Halfday</button>
                                <button type="button" class="btn btn-warning waves-effect waves-light">Extra Warning</button>
                            </div>
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
            <h5 class="mb-3">Month leave Opening Balance</h5>
            <div class="row">
                <div class="col-xl-2 col-md-4">
                    <div class="form-group">
                        <label>CL</label>
                        <input type="text" class="form-control" value="{{ $clBalance }}" readonly>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="form-group">
                        <label>Extra Working (Balance)</label>
                        <input type="text" class="form-control" value="0.00" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h5 class="mb-3">Attendance List</h5>
            <div class="table-responsive">
                <table class="table table_row_btn mb-0" id="attendance-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Employee Name</th>
                            <th>Day</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Working Hours</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-tbody">
                        <tr>
                            <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function loadAttendance() {
        var month = $('#filter_month').val();
        var year = $('#filter_year').val();
        
        $.ajax({
            url: '{{ route("employee.attendance.data") }}',
            type: 'GET',
            data: { month: month, year: year },
            success: function(response) {
                if (response.success && response.data) {
                    var tbody = $('#attendance-tbody');
                    tbody.empty();
                    
                    response.data.forEach(function(item, index) {
                        var row = '<tr class="' + item.row_class + '">' +
                            '<th scope="row">' + (index + 1) + '</th>' +
                            '<td>' + item.date + '</td>' +
                            '<td>' + '{{ $employee->full_name }}' + '</td>' +
                            '<td>' + item.day + '</td>' +
                            '<td>' + item.check_in + '</td>' +
                            '<td>' + item.check_out + '</td>' +
                            '<td>' + item.working_hours + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                }
            },
            error: function(xhr) {
                $('#attendance-tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading attendance data.</td></tr>');
            }
        });
    }

    // Load attendance on page load
    loadAttendance();

    // Reload when month/year changes
    $('#filter_month, #filter_year').on('change', function() {
        loadAttendance();
    });
});
</script>
@endpush


