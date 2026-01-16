@extends('layouts.app')

@section('title', 'View Service Record')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">View Service Record</h4>
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
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('admin.service-records.index') }}" class="btn btn-secondary">Back to List</a>
        <a href="{{ route('admin.service-records.edit', $serviceRecord) }}" class="btn btn-primary">Edit</a>
    </div>
</div>
@endsection

