<div class="row">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">Leave Information</h6>
        <table class="table table-bordered">


<?php 
//echo "<pre>"; print_r($leave); die();
?>
            <tr>
                <th style="width: 30%;">Employee</th>
                <td>
                    @if($leave->employee)
                        {{ $leave->employee->full_name }} ({{ $leave->employee->employee_id }})
                    @else
                        N/A
                    @endif
                </td>
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
                <td>
                    @if($leave->from_date)
                        @if(is_string($leave->from_date))
                            {{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}
                        @else
                            {{ $leave->from_date->format('d-m-Y') }}
                        @endif
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>To Date</th>
                <td>
                    @if($leave->to_date)
                        @if(is_string($leave->to_date))
                            {{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}
                        @else
                            {{ $leave->to_date->format('d-m-Y') }}
                        @endif
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total Days</th>
                <td>{{ $leave->total_days ?? 0 }} day(s)</td>
            </tr>
            <tr>
                <th>Reason</th>
                <td>{{ $leave->reason ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Statusss {{$leave->status}}</th>
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
                <td>
                    @if($leave->approved_at)
                        @if(is_string($leave->approved_at))
                            {{ \Carbon\Carbon::parse($leave->approved_at)->format('Y-m-d H:i:s') }}
                        @else
                            {{ $leave->approved_at->format('Y-m-d H:i:s') }}
                        @endif
                    @else
                        N/A
                    @endif
                </td>
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
                <td>
                    @if($leave->created_at)
                        @if(is_string($leave->created_at))
                            {{ \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d H:i:s') }}
                        @else
                            {{ $leave->created_at->format('Y-m-d H:i:s') }}
                        @endif
                    @else
                        N/A
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

