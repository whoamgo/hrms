<div class="row">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">Service Record Information</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Employee</th>
                <td>{{ $serviceRecord->employee->full_name }} ({{ $serviceRecord->employee->employee_id }})</td>
            </tr>
            <tr>
                <th>Period</th>
                <td>
                    {{ $serviceRecord->from_date->format('d-m-Y') }}
                    @if($serviceRecord->to_date)
                        to {{ $serviceRecord->to_date->format('d-m-Y') }}
                    @else
                        to Present
                    @endif
                </td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $serviceRecord->designation }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $serviceRecord->department }}</td>
            </tr>
            <tr>
                <th>Remarks</th>
                <td>{{ $serviceRecord->remarks ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($serviceRecord->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $serviceRecord->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $serviceRecord->updated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>

