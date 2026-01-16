@extends('layouts.app')

@section('title', 'View Leave')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">View Leave</h4>
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
                        <td>{{ $leave->employee->full_name }} ({{ $leave->employee->employee_id }})</td>
                    </tr>
                    <tr>
                        <th>Leave Type</th>
                        <td>
                            @if($leave->leave_type == 'CL')
                                Casual Leave (CL)
                            @elseif($leave->leave_type == 'SL')
                                Sick Leave (SL)
                            @else
                                Special Leave
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>From Date</th>
                        <td>{{ $leave->from_date->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>To Date</th>
                        <td>{{ $leave->to_date->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Total Days</th>
                        <td>{{ $leave->total_days }} day(s)</td>
                    </tr>
                    <tr>
                        <th>Reason</th>
                        <td>{{ $leave->reason ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($leave->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($leave->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @if($leave->approved_by)
                    <tr>
                        <th>Approved By</th>
                        <td>{{ $leave->approver->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Approved At</th>
                        <td>{{ $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    </tr>
                    @endif
                    @if($leave->rejection_reason)
                    <tr>
                        <th>Rejection Reason</th>
                        <td>{{ $leave->rejection_reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created At</th>
                        <td>{{ $leave->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('hr.leaves.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('hr.leaves.edit', $leave) }}" class="btn btn-primary">Edit</a>
    </div>
</div>
@endsection

