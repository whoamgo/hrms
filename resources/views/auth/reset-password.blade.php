<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Reset Password - HRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />
</head>

<body class="authentication-bg">
    <div class="account-pages">
        <div class="container container_login">
            <div class="row">
                <div class="col-md-6 col-lg-4 col-xl-4">
                    <div class="account-card-box">
                        <div class="card mb-0">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <div class="my-3">
                                        <a href="javascript:void(0);">
                                            <span><img src="{{ asset('assets/images/logo.png') }}" alt="" height="auto"></span>
                                        </a>
                                    </div>
                                    <h5 class="text-muted text-uppercase py-3 font-16"> Reset Password</h5>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    
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
</body>
</html>

