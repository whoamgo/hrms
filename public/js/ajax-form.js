/**
 * AJAX Form Handler
 * Handles form submissions via AJAX with validation
 */
(function($) {
    'use strict';

    $.fn.ajaxForm = function(options) {
        var defaults = {
            successMessage: 'Operation completed successfully.',
            errorMessage: 'An error occurred. Please try again.',
            redirect: null,
            onSuccess: null,
            onError: null,
            showLoader: true,
            resetForm: true
        };

        var settings = $.extend({}, defaults, options);

        return this.each(function() {
            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
            var originalBtnText = $submitBtn.html();

            $form.on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $form.find('.error-text').text('').hide();
                $form.find('.alert-danger').remove();
                $form.find('.alert-success').remove();
                $form.find('.form-control').removeClass('is-invalid');

                // Show loader
                if (settings.showLoader) {
                    $submitBtn.prop('disabled', true);
                    if ($submitBtn.find('.spinner-border-sm').length === 0) {
                        $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    } else {
                        $submitBtn.find('.spinner-border-sm').show();
                    }
                }

                var formMethod = $form.attr('method') || 'POST';
                var formAction = $form.attr('action') || window.location.href;
                
                // Check for _method field (Laravel method spoofing for PUT/PATCH/DELETE)
                var methodField = $form.find('input[name="_method"]');
                var actualMethod = formMethod;
                if (methodField.length) {
                    actualMethod = methodField.val();
                }

                var formData;
                var hasFiles = $form.find('input[type="file"]').length > 0;
                
                // Convert 12-hour time format to 24-hour format for time inputs
                $form.find('.timepicker').each(function() {
                    var $timeInput = $(this);
                    var timeValue = $timeInput.val();
                    if (timeValue) {
                        // Check if time contains AM/PM
                        var match = timeValue.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                        if (match) {
                            var hours = parseInt(match[1]);
                            var minutes = match[2];
                            var ampm = match[3].toUpperCase();
                            
                            if (ampm === 'PM' && hours !== 12) {
                                hours += 12;
                            } else if (ampm === 'AM' && hours === 12) {
                                hours = 0;
                            }
                            
                            // Set 24-hour format value
                            var formatted24 = (hours < 10 ? '0' : '') + hours + ':' + minutes;
                            $timeInput.val(formatted24);
                        }
                    }
                });
                
                // Handle file uploads
                if (hasFiles) {
                    formData = new FormData($form[0]);
                    // Ensure _token is included
                    if (!formData.has('_token')) {
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    }
                } else {
                    formData = $form.serialize();
                }

                $.ajax({
                    url: formAction,
                    type: actualMethod,
                    data: formData,
                    processData: !hasFiles,
                    contentType: hasFiles ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
                    dataType: 'json',
                    success: function(response) {
                        if (settings.showLoader) {
                            $submitBtn.prop('disabled', false);
                            $submitBtn.html(originalBtnText);
                        }

                        if (response.success) {
                            // Show success message
                            if (response.message) {
                                showAlert('success', response.message);
                            } else {
                                showAlert('success', settings.successMessage);
                            }

                            // Reset form if needed
                            if (settings.resetForm) {
                                $form[0].reset();
                            }

                            // Callback
                            if (settings.onSuccess) {
                                settings.onSuccess(response);
                            }

                            // Redirect
                            if (response.redirect || settings.redirect) {
                                setTimeout(function() {
                                    window.location.href = response.redirect || settings.redirect;
                                }, 1000);
                            } else {
                                // Reload page after 1 second
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        } else {
                            showAlert('error', response.message || settings.errorMessage);
                            if (settings.onError) {
                                settings.onError(response);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (settings.showLoader) {
                            $submitBtn.prop('disabled', false);
                            $submitBtn.html(originalBtnText);
                        }

                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(key, value) {
                                // Handle array fields like permissions.0, menu_items.0
                                var cleanKey = key.replace(/\.\d+$/, '').replace(/\./g, '_');
                                var errorElement = $form.find('.' + cleanKey + '_error');
                                
                                if (errorElement.length) {
                                    errorElement.text(value[0] || value).show();
                                } else {
                                    // Try to find input and show error
                                    var input = $form.find('[name="' + key + '"]');
                                    if (input.length) {
                                        var errorSpan = input.siblings('.error-text').first();
                                        if (!errorSpan.length) {
                                            input.after('<span class="text-danger error-text ' + cleanKey + '_error">' + (value[0] || value) + '</span>');
                                        } else {
                                            errorSpan.text(value[0] || value).show();
                                        }
                                    } else {
                                        // For array fields, show error near the section
                                        if (key.includes('permissions')) {
                                            var permError = $form.find('.permissions_error');
                                            if (!permError.length) {
                                                $form.find('h5:contains("Permissions")').after('<span class="text-danger error-text permissions_error">' + (value[0] || value) + '</span>');
                                            } else {
                                                permError.text(value[0] || value).show();
                                            }
                                        } else if (key.includes('menu_items')) {
                                            var menuError = $form.find('.menu_items_error');
                                            if (!menuError.length) {
                                                $form.find('h5:contains("Menu Items")').after('<span class="text-danger error-text menu_items_error">' + (value[0] || value) + '</span>');
                                            } else {
                                                menuError.text(value[0] || value).show();
                                            }
                                        }
                                    }
                                }
                            });
                            showAlert('error', 'Please fix the validation errors.');
                        } else {
                            var message = xhr.responseJSON?.message || settings.errorMessage;
                            showAlert('error', message);
                        }

                        if (settings.onError) {
                            settings.onError(xhr);
                        }
                    }
                });
            });
        });
    };

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var prefix = type === 'success' ? 'Success:' : 'Error:';
        var alertHtml = '<div class="row flash-message-row">' +
            '<div class="col-12">' +
            '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<strong>' + prefix + '</strong> ' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';

        // Remove existing dynamically added alerts (not session flash messages)
        $('.flash-message-row').remove();

        // Find the content container-fluid (not footer)
        var $contentContainer = $('.content-page .content .container-fluid').first();
        
        if ($contentContainer.length) {
            // Add new alert at the top of the content container-fluid, after page title if exists
            var $pageTitle = $contentContainer.find('.page-title-box').closest('.row');
            if ($pageTitle.length) {
                $pageTitle.after(alertHtml);
            } else {
                $contentContainer.prepend(alertHtml);
            }
        } else {
            // Fallback to body
            $('body').prepend(alertHtml);
        }

        // Auto hide after 15 seconds
        setTimeout(function() {
            $('.flash-message-row').fadeOut(function() {
                $(this).remove();
            });
        }, 15000);
    }

    // Auto-initialize forms with data-ajax-form attribute
    $(document).ready(function() {
        $('form[data-ajax-form]').each(function() {
            var $form = $(this);
            var options = {};
            
            if ($form.data('success-message')) {
                options.successMessage = $form.data('success-message');
            }
            if ($form.data('redirect')) {
                options.redirect = $form.data('redirect');
            }
            if ($form.data('reset-form') !== undefined) {
                options.resetForm = $form.data('reset-form');
            }

            $form.ajaxForm(options);
        });
    });

})(jQuery);

