@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-box">
            <div class="text-center">
                <h4 class="text-uppercase font-bold mb-4">Reset Password</h4>
            </div>

            <form id="reset-password-form" action="{{ route('password.update') }}" method="POST" data-ajax-form>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label>Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                    <span class="text-danger error-text email_error"></span>
                </div>

                <div class="form-group">
                    <label>New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="text-danger error-text password_error"></span>
                </div>

                <div class="form-group">
                    <label>Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        Reset Password
                    </button>
                </div>

                <div class="form-group text-center">
                    <a href="{{ route('login') }}" class="text-muted">
                        <i class="mdi mdi-arrow-left"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var spinner = submitBtn.find('.spinner-border');
        
        spinner.show();
        submitBtn.prop('disabled', true);
        $('.error-text').text('');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key.replace('.', '_') + '_error').text(value[0]);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                }
            },
            complete: function() {
                spinner.hide();
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush

