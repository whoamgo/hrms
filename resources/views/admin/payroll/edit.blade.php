@extends('layouts.app')

@section('title', 'Edit Payslip')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Payslip</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="payslip-form" data-ajax-form>
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Employee:</strong> {{ $payslip->employee->full_name ?? 'N/A' }}</p>
                        <p><strong>Month:</strong> {{ $payslip->month }} {{ $payslip->year }}</p>
                    </div>
                </div>
                <hr>

                <h5 class="input_top_hd">Earnings</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Basic Salary <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="basic_salary" name="basic_salary" value="{{ $payslip->basic_salary }}" required>
                            <span class="text-danger error-text basic_salary_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>HRA</label>
                            <input type="number" step="0.01" class="form-control" id="hra" name="hra" value="{{ $payslip->hra }}">
                            <span class="text-danger error-text hra_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Conveyance Allowance</label>
                            <input type="number" step="0.01" class="form-control" id="conveyance_allowance" name="conveyance_allowance" value="{{ $payslip->conveyance_allowance }}">
                            <span class="text-danger error-text conveyance_allowance_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Medical Allowance</label>
                            <input type="number" step="0.01" class="form-control" id="medical_allowance" name="medical_allowance" value="{{ $payslip->medical_allowance }}">
                            <span class="text-danger error-text medical_allowance_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Special Allowance</label>
                            <input type="number" step="0.01" class="form-control" id="special_allowance" name="special_allowance" value="{{ $payslip->special_allowance }}">
                            <span class="text-danger error-text special_allowance_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">Deductions</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>ESI</label>
                            <input type="number" step="0.01" class="form-control" id="esi" name="esi" value="{{ $payslip->esi }}">
                            <span class="text-danger error-text esi_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>PF</label>
                            <input type="number" step="0.01" class="form-control" id="pf" name="pf" value="{{ $payslip->pf }}">
                            <span class="text-danger error-text pf_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>TDS</label>
                            <input type="number" step="0.01" class="form-control" id="tds" name="tds" value="{{ $payslip->tds }}">
                            <span class="text-danger error-text tds_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Other Deductions</label>
                            <input type="number" step="0.01" class="form-control" id="other_deductions" name="other_deductions" value="{{ $payslip->other_deductions ?? 0 }}">
                            <span class="text-danger error-text other_deductions_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Mobile Deduction</label>
                            <input type="number" step="0.01" class="form-control" id="mobile_deduction" name="mobile_deduction" value="{{ $payslip->mobile_deduction }}">
                            <span class="text-danger error-text mobile_deduction_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Comp Off</label>
                            <input type="number" step="0.01" class="form-control" id="comp_off" name="comp_off" value="{{ $payslip->comp_off }}">
                            <span class="text-danger error-text comp_off_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Days Payable</label>
                            <input type="number" step="0.01" class="form-control" id="days_payable" name="days_payable" value="{{ $payslip->days_payable ?? 0 }}">
                            <span class="text-danger error-text days_payable_error"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Update Payslip
                    </button>
                    <a href="{{ route('admin.payroll.index') }}" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#payslip-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: '{{ route("admin.payroll.update", $payslip->id) }}',
            type: 'PUT',
            data: formData + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    //alert(response.message);
                    window.location.href = response.redirect || '{{ route("admin.payroll.index") }}';
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key.replace('.', '_') + '_error').text(value[0]);
                    });
                }
            }
        });
    });
});
</script>
@endpush

