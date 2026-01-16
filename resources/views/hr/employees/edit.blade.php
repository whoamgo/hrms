@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Employee</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="employee-form" action="{{ route('hr.employees.update', $employee) }}" method="POST" data-ajax-form enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h5 class="input_top_hd">Personal Details</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employee Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="employee_type" name="employee_type" required>
                                <option value="">Select Employee Type</option>
                                <option value="Permanent" {{ $employee->employee_type == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="Contract" {{ $employee->employee_type == 'Contract' ? 'selected' : '' }}>Contract</option>
                            </select>
                            <span class="text-danger error-text employee_type_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $employee->full_name }}" required>
                            <span class="text-danger error-text full_name_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Father/Mother Name</label>
                            <input type="text" class="form-control" id="father_mother_name" name="father_mother_name" value="{{ $employee->father_mother_name }}">
                            <span class="text-danger error-text father_mother_name_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>DOB</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="{{ $employee->dob ? $employee->dob->format('Y-m-d') : '' }}">
                            <span class="text-danger error-text dob_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Transgender" {{ $employee->gender == 'Transgender' ? 'selected' : '' }}>Transgender</option>
                                <option value="Other" {{ $employee->gender == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <span class="text-danger error-text gender_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ $employee->mobile_number }}" required>
                            <span class="text-danger error-text mobile_number_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="employee_email" name="employee_email" value="{{ $employee->email }}" required>
                            <small class="form-text text-muted">This will update the linked user account email.</small>
                            <span class="text-danger error-text employee_email_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ $employee->address }}">
                            <span class="text-danger error-text address_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">Bank Information</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ $employee->account_holder_name }}">
                            <span class="text-danger error-text account_holder_name_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Bank Account Number</label>
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ $employee->bank_account_number }}">
                            <span class="text-danger error-text bank_account_number_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ $employee->bank_name }}">
                            <span class="text-danger error-text bank_name_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Bank Branch Name</label>
                            <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name" value="{{ $employee->bank_branch_name }}">
                            <span class="text-danger error-text bank_branch_name_error"></span>
                        </div>
                    </div>   
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ $employee->ifsc_code }}">
                            <span class="text-danger error-text ifsc_code_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>PAN Card Number</label>
                            <input type="text" class="form-control" id="pan_card_number" name="pan_card_number" value="{{ $employee->pan_card_number }}">
                            <span class="text-danger error-text pan_card_number_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">Official Details</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text department_id_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Designation</label>
                            <select class="form-control" id="designation_id" name="designation_id">
                                <option value="">Select Designation</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}" {{ $employee->designation_id == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text designation_id_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Date of Joining</label>
                            <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="{{ $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '' }}">
                            <span class="text-danger error-text date_of_joining_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Employment Status</label>
                            <input type="text" class="form-control" id="employment_status" name="employment_status" value="{{ $employee->employment_status }}">
                            <span class="text-danger error-text employment_status_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">Contract Details (Conditional)</h5>
                <div class="row">
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Contract Start Date</label>
                            <input type="date" class="form-control" id="contract_start_date" name="contract_start_date" value="{{ $employee->contract_start_date ? $employee->contract_start_date->format('Y-m-d') : '' }}">
                            <span class="text-danger error-text contract_start_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Contract End Date</label>
                            <input type="date" class="form-control" id="contract_end_date" name="contract_end_date" value="{{ $employee->contract_end_date ? $employee->contract_end_date->format('Y-m-d') : '' }}">
                            <span class="text-danger error-text contract_end_date_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">Documents</h5>
                <div class="row">
                    <div class="col-xl-6 col-md-4">
                        <div class="form-group file_upload">
                            <label>Upload Appointment Letter</label>
                            @if($employee->appointment_letter)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $employee->appointment_letter) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-file"></i> View Current File
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="appointment_letter" name="appointment_letter" accept=".pdf,.doc,.docx">
                            <small class="form-text text-muted">Max size: 5MB. Allowed: PDF, DOC, DOCX</small>
                            <span class="text-danger error-text appointment_letter_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-4">
                        <div class="form-group file_upload">
                            <label>Upload ID Proof</label>
                            @if($employee->id_proof)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $employee->id_proof) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-file"></i> View Current File
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control" id="id_proof" name="id_proof" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Max size: 5MB. Allowed: PDF, DOC, DOCX, JPG, PNG</small>
                            <span class="text-danger error-text id_proof_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">User Account</h5>
                <div class="row">
                    @if($employee->user)
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>User Account</label>
                            <input type="text" class="form-control" value="{{ $employee->user->name }} ({{ $employee->user->email }})" readonly>
                            <small class="form-text text-muted">Username: {{ $employee->user->username }} | Role: {{ $employee->user->role->name ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Change Password (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters.</small>
                            <span class="text-danger error-text password_error"></span>
                        </div>
                    </div>
                    @else
                    <div class="col-xl-12">
                        <div class="alert alert-warning">
                            <strong>Note:</strong> No user account is linked to this employee. User account should have been created automatically.
                        </div>
                    </div>
                    @endif
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <span class="text-danger error-text status_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-md-12">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Update
                            </button>
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide contract fields based on employee type
    $('#employee_type').on('change', function() {
        if ($(this).val() === 'Contract') {
            $('#contract_start_date').prop('required', true);
        } else {
            $('#contract_start_date').prop('required', false);
        }
    });
    
    // Set required on page load if contract type
    if ($('#employee_type').val() === 'Contract') {
        $('#contract_start_date').prop('required', true);
    }
});
</script>
@endpush

