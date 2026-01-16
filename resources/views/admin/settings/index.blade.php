@extends('layouts.app')

@section('title', 'Settings Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Settings Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="settings-form" data-ajax-form enctype="multipart/form-data">
                @csrf
                
                <h5 class="input_top_hd">Company Information</h5>
                <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $settings['company_name'] ?? config('app.name', 'HRMS') }}">
                            <span class="text-danger error-text company_name_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Company Email</label>
                            <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $settings['company_email'] ?? '' }}">
                            <span class="text-danger error-text company_email_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Company Phone</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}">
                            <span class="text-danger error-text company_phone_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Tax ID / GST Number</label>
                            <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ $settings['tax_id'] ?? '' }}">
                            <span class="text-danger error-text tax_id_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12">
                        <div class="form-group">
                            <label>Company Address</label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="3">{{ $settings['company_address'] ?? '' }}</textarea>
                            <span class="text-danger error-text company_address_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Company Logo</label>
                            <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                            @if(isset($settings['company_logo']) && $settings['company_logo'])
                                <small class="form-text text-muted">Current logo: <a href="{{ asset('storage/' . $settings['company_logo']) }}" target="_blank">View</a></small>
                            @endif
                            <span class="text-danger error-text company_logo_error"></span>
                        </div>
                    </div>
                </div>

                <h5 class="input_top_hd">System Settings</h5>
                <div class="row">
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="INR" {{ ($settings['currency'] ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            </select>
                            <span class="text-danger error-text currency_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Date Format</label>
                            <select class="form-control" id="date_format" name="date_format">
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? 'd/m/Y') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d-m-Y" {{ ($settings['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                            </select>
                            <span class="text-danger error-text date_format_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="form-group">
                            <label>Timezone</label>
                            <select class="form-control" id="timezone" name="timezone">
                                <option value="Asia/Kolkata" {{ ($settings['timezone'] ?? 'Asia/Kolkata') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                <option value="Europe/London" {{ ($settings['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                            </select>
                            <span class="text-danger error-text timezone_error"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.settings.update") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('.error-text').text('');
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

