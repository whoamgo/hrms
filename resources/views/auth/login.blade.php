<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login - HRMS</title>
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
                                    <h5 class="text-muted text-uppercase py-3 font-16">Sign In</h5>
                                </div>
    
                                <form id="login-form" class="mt-2">
                                    <div id="login-error" class="alert alert-danger" style="display: none;"></div>
                                    <div id="login-success" class="alert alert-success" style="display: none;"></div>

                                    <div class="form-group mb-3">
                                        <input class="form-control" type="text" name="username" id="username" required placeholder="Enter your email">
                                        <span class="text-danger error-text username_error"></span>
                                    </div>
    
                                    <div class="form-group mb-3">
                                        <input class="form-control" type="password" name="password" id="password" required placeholder="Enter your password">
                                        <span class="text-danger error-text password_error"></span>
                                    </div>
    
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkbox-signin" name="remember">
                                            <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div>
 
    
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-primary loginbtn btn-block waves-effect waves-light">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                            Log In
                                        </button>
                                    </div>
                                    <p class="text-center"> 
                                        <a href="{{ route('password.request') }}" class="text-muted">
                                            <i class="mdi mdi-lock mr-1"></i> Forgot your password?
                                        </a> 
                                    </p>
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
            // CSRF Token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Login form submission
            $('#login-form').on('submit', function(e) {
                e.preventDefault();

                
                
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var spinner = submitBtn.find('.spinner-border-sm');
                var errorDiv = $('#login-error');
                var successDiv = $('#login-success');
                
                // Clear previous errors
                $('.error-text').text('');
                errorDiv.hide().text('');
                successDiv.hide().text('');
                
                // Show spinner
                spinner.show();
                submitBtn.prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("login") }}',
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        spinner.hide();
                        submitBtn.prop('disabled', false);
                        
                        if (response.success) {
                            successDiv.text(response.message).show();
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 500);
                        } else {
                            errorDiv.text(response.message || 'Login failed').show();
                        }
                    },
                    error: function(xhr) {
                        spinner.hide();
                        submitBtn.prop('disabled', false);
                        
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('.' + key + '_error').text(value[0]);
                            });
                        } else {
                            var message = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                            errorDiv.text(message).show();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

