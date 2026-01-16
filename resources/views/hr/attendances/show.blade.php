@extends('layouts.app')

@section('title', 'View Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">View Attendance</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%;">Employee</th>
                        <td>{{ $attendance->employee->full_name }} ({{ $attendance->employee->employee_id }})</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Day</th>
                        <td>
                            {{ $attendance->date->format('l') }}
                            @if($attendance->status == 'weekend' || $attendance->status == 'holiday')
                                ({{ ucfirst($attendance->status) }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge badge-success">Present</span>
                            @elseif($attendance->status == 'absent')
                                <span class="badge badge-danger">Absent</span>
                            @elseif($attendance->status == 'holiday')
                                <span class="badge badge-info">Holiday</span>
                            @else
                                <span class="badge badge-secondary">Weekend</span>
                            @endif
                        </td>
                    </tr>
            <tr>
                <th>Check In</th>
                <td>{{ $attendance->check_in ? (is_string($attendance->check_in) ? $attendance->check_in : \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s')) : '-' }}</td>
            </tr>
            <tr>
                <th>Check Out</th>
                <td>{{ $attendance->check_out ? (is_string($attendance->check_out) ? $attendance->check_out : \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s')) : '-' }}</td>
            </tr>
                    <tr>
                        <th>Working Hours</th>
                        <td>{{ $attendance->calculateWorkingHours() }}</td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td>{{ $attendance->remarks ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $attendance->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('hr.attendances.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('hr.attendances.edit', $attendance) }}" class="btn btn-primary">Edit</a>
    </div>
</div>
@endsection

