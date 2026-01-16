@extends('layouts.app')

@section('title', 'Add Service Record')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Add Service Record</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="service-record-form" action="{{ route('admin.service-records.store') }}" method="POST" data-ajax-form>
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
                            <label>From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                            <span class="text-danger error-text from_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date">
                            <span class="text-danger error-text to_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Designation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="designation" name="designation" required>
                            <span class="text-danger error-text designation_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="department" name="department" required>
                            <span class="text-danger error-text department_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            <span class="text-danger error-text remarks_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span class="text-danger error-text status_error"></span>
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
                            <a href="{{ route('admin.service-records.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

