@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Attendance</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="attendance-form" action="{{ route('admin.attendances.update', $attendance) }}" method="POST" data-ajax-form>
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select class="form-control" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $attendance->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text employee_id_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $attendance->date->format('Y-m-d') }}" required>
                            <span class="text-danger error-text date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="holiday" {{ $attendance->status == 'holiday' ? 'selected' : '' }}>Holiday</option>
                                <option value="weekend" {{ $attendance->status == 'weekend' ? 'selected' : '' }}>Weekend</option>
                            </select>
                            <span class="text-danger error-text status_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Check In</label>
                            <input type="time" class="form-control" id="check_in" name="check_in" value="{{ $attendance->check_in ? (is_string($attendance->check_in) ? substr($attendance->check_in, 0, 5) : \Carbon\Carbon::parse($attendance->check_in)->format('H:i')) : '' }}">
                            <span class="text-danger error-text check_in_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Check Out</label>
                            <input type="time" class="form-control" id="check_out" name="check_out" value="{{ $attendance->check_out ? (is_string($attendance->check_out) ? substr($attendance->check_out, 0, 5) : \Carbon\Carbon::parse($attendance->check_out)->format('H:i')) : '' }}">
                            <span class="text-danger error-text check_out_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-8">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $attendance->remarks }}</textarea>
                            <span class="text-danger error-text remarks_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Update
                            </button>
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

