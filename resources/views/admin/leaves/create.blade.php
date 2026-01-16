@extends('layouts.app')

@section('title', 'Add Leave')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Add Leave</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="leave-form" action="{{ route('admin.leaves.store') }}" method="POST" data-ajax-form>
                @csrf
                
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select class="form-control" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_id }})</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text employee_id_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="leave_type" name="leave_type" required>
                                <option value="">Select Leave Type</option>
                                <option value="CL">Casual Leave (CL)</option>
                                <option value="SL">Sick Leave (SL)</option>
                                <option value="SPL">Special Leave</option>
                            </select>
                            <span class="text-danger error-text leave_type_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Day Type</label>
                            <select class="form-control" id="day_type" name="day_type">
                                <option value="Full">Full Day</option>
                                <option value="Half">Half Day</option>
                            </select>
                            <span class="text-danger error-text day_type_error"></span>
                        </div>
                    </div>
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
                    <div class="col-xl-6 col-md-8">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                            <span class="text-danger error-text reason_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Save
                            </button>
                            <a href="{{ route('admin.leaves.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

