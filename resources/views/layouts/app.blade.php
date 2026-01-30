<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'HRMS')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />
    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}" integrity="sha384-5IbgsdqrjF6rAX1mxBZkKRyUOgEr0/xCGkteJIaRKpvW0Ag0tf6lru4oL2ZhcMvo" crossorigin="anonymous">
    <!-- Bootstrap Timepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-timepicker.min.css') }}" integrity="sha384-nLzLnfaWi3Ujpb0PFRLqdV4G3JN644XICJLid2lSwVICK0giayOdnsScYbeiDlu8" crossorigin="anonymous">

    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}" integrity="sha384-R334r6kryDNB/GWs2kfB6blAOyWPCxjdHSww/mo7fel+o5TM/AOobJ0QpGRXSDh4" crossorigin="anonymous">

    <style>
        /* Date input with icon */
        .date-input-wrapper {
            position: relative;
        }
        .date-input-wrapper .form-control {
            padding-right: 40px;
        }
        .date-input-wrapper .date-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: all;
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            font-size: 18px;
        }
        .date-input-wrapper .date-icon:hover {
            color: #495057;
        }
        /* Time input with icon */
        .time-input-wrapper {
            position: relative;
        }
        .time-input-wrapper .form-control {
            padding-right: 40px;
        }
        .time-input-wrapper .time-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: all;
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
            font-size: 18px;
        }
        .time-input-wrapper .time-icon:hover {
            color: #495057;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topnav-menu float-right mb-0">
               <!--  <li><button id="clearCacheBtn" class="btn btn-danger">
                Clear Cache
                </button>
                </li> -->


                    <li class="notification-list">
                       <a id="clearCacheBtn" class="nav-link waves-light" href="javascript:void(0)" title="Clear Cache &amp; Refresh">
                       <i class="mdi mdi-refresh noti-icon" style="
                          color: #ffffff;
                          font-size: x-large;
                          margin: 10px;
                          "></i>
                       </a>
                    </li>




                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="mdi mdi-bell-outline noti-icon"></i> 
                        <span class="noti-icon-badge"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                        <div class="dropdown-item noti-title">
                            <h5 class="font-16 text-white m-0">
                                <span class="float-right"> 
                                    <a href="#" class="text-white"> <small>Clear All</small> </a>
                                </span>Notification
                            </h5>
                        </div>
                        <div class="slimscroll noti-scroll" id="notifications-list">
                            @php
                                $notifications = auth()->check() && auth()->user() ? auth()->user()->unreadNotifications()->take(5)->get() : collect([]);
                            @endphp
                            @forelse($notifications as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item notify-item mark-as-read" data-id="{{ $notification->id }}">
                                    <div class="notify-icon bg-{{ $notification->data['type'] ?? 'info' }}">
                                        <i class="mdi mdi-bell-outline"></i>
                                    </div>
                                    <p class="notify-details">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                        <small class="text-muted">{{ $notification->data['message'] ?? '' }}</small>
                                    </p>
                                </a>
                            @empty
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <p class="notify-details">No new notifications</p>
                                </a>
                            @endforelse
                        </div>
                        <a href="{{ route('notifications.index') }}" class="dropdown-item text-primary notify-item notify-all"> 
                            View all <i class="fi-arrow-right"></i>
                        </a>
                    </div>
                </li>
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ auth()->check() && auth()->user() && auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/images/users/avatar-1.jpg') }}" alt="user-image" class="rounded-circle">
                        <div class="d-none d-sm-inline-block ml-1 font-weight-medium"> 
                            <span>{{ auth()->check() && auth()->user() ? auth()->user()->name : 'Guest' }}</span>  
                            <span> ({{ auth()->check() && auth()->user() && auth()->user()->role ? auth()->user()->role->name : 'No Role' }}) </span>
                        </div>
                        <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                        <a href="{{ route('profile.index') }}" class="dropdown-item notify-item"> 
                            <i class="mdi mdi-account-settings"></i> <span>Profile</span> 
                        </a>
                        <a href="{{ route('change-password.index') }}" class="dropdown-item notify-item"> 
                            <i class="mdi mdi-lock-outline"></i> <span>Change Password</span> 
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item notify-item" id="logout-btn"> 
                            <i class="mdi mdi-logout-variant"></i> <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>

            <!-- LOGO -->
            <div class="logo-box">
                <a href="{{ route('dashboard') }}" class="logo text-center logo-dark">
                    <span class="logo-lg"> <img src="{{ asset('assets/images/logo.png') }}" alt="" height="auto"> </span>
                    <span class="logo-sm"> <img src="{{ asset('assets/images/logo_sm.png') }}" alt="" height="auto"> </span>
                </a>
            </div>
            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <li><button class="button-menu-mobile waves-effect waves-light"> <i class="mdi mdi-menu"></i> </button></li>
            </ul>
        </div>
        <!-- end Topbar -->
        
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">
            <div class="slimscroll-menu">
                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <ul class="metismenu" id="side-menu">
                        @include('components.sidebar-menu')
                    </ul>
                </div>
                <!-- End Sidebar -->
                <div class="clearfix"></div>
            </div>
            <!-- Sidebar -left -->
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            2025 &copy; SSAAT theme by <a href="#">SSAAT</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    
    <!-- Toastr JS -->
   <script src="{{ asset('assets/js/toastr.min.js') }}" integrity="sha384-Si3HKTyQYGU+NC4aAF3ThcOSvK+ZQiyEKlYyfjiIFKMqsnCmfHjGa1VK1kYP9UdS" crossorigin="anonymous"></script>

    <!-- Bootstrap Datepicker JS -->
    <script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}" integrity="sha384-duAtk5RV7s42V6Zuw+tRBFcqD8RjRKw6RFnxmxIj1lUGAQJyum/vtcUQX8lqKQjp" crossorigin="anonymous"></script>
    <!-- Bootstrap Timepicker JS -->
    <script src="{{ asset('assets/js/bootstrap-timepicker.min.js') }}"  integrity="sha384-j0kMAC9Ymu1pP+W9AURKMmsgAhiaJJCdBKGCqmpXMdeaOz+T1y6vHtcUlrobCB6G" crossorigin="anonymous"></script>
    
    <!-- AJAX Form Handler -->
    <script src="{{ asset('js/ajax-form.js') }}"></script>
  

    <!-- Custom JS -->
    <script>

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        extendedTimeOut: 1000,
        preventDuplicates: true,   // 🔥 IMPORTANT
        newestOnTop: true,
        showDuration: 300,
        hideDuration: 300,
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

 
        document.getElementById('clearCacheBtn').addEventListener('click', function () {
            if (!confirm('Are you sure you want to clear cache?')) {
                return;
            }

            fetch("{{ route('clear.cache') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload(); // Refresh page
                }
            })
            .catch(error => console.error(error));
        });
   


        // CSRF Token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Logout functionality
        $(document).on('click', '#logout-btn', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                $.ajax({
                    url: '{{ route("logout") }}',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function() {
                        toastr.error('Error during logout. Please try again.');
                        //alert('Error during logout. Please try again.');
                    }
                });
            }
        });

        // Mark notification as read
        $(document).on('click', '.mark-as-read', function(e) {
            var notificationId = $(this).data('id');
            if (notificationId) {
                $.ajax({
                    url: '/notifications/' + notificationId + '/read',
                    type: 'POST',
                    success: function() {
                        // Notification marked as read
                    }
                });
            }
        });

        // Load notifications count
        function loadNotificationsCount() {
            $.ajax({
                url: '{{ route("notifications.unread-count") }}',
               // url: '/notifications/unread-count',
                type: 'GET',
                success: function(response) {
                    if (response.count > 0) {
                        $('.noti-icon-badge').text(response.count).show();
                    } else {
                        $('.noti-icon-badge').hide();
                    }
                }
            });
        }

        // Load notifications count on page load
        loadNotificationsCount();
        
        // Refresh notifications count every 30 seconds
        setInterval(loadNotificationsCount, 30000);

        // Ensure session messages only appear in content area, not footer
        $(document).ready(function() {
            // Remove any messages that appear in footer
            $('.footer .container-fluid .session-message-row').remove();
            $('.footer .container-fluid .flash-message-row').remove();
            
            // Ensure messages only appear once in content area
            var seenMessages = [];
            $('.content-page .content .container-fluid .session-message-row').each(function() {
                var messageText = $(this).find('.alert').text().trim();
                if (seenMessages.indexOf(messageText) !== -1) {
                    $(this).remove();
                } else {
                    seenMessages.push(messageText);
                }
            });

            // Function to add icon wrapper to date inputs
            function wrapDateInputs() {
                $('input[type="date"]').not('.date-wrapped').each(function() {
                    var $input = $(this);
                    if (!$input.parent().hasClass('date-input-wrapper')) {
                        $input.wrap('<div class="date-input-wrapper"></div>');
                        var $icon = $('<i class="mdi mdi-calendar date-icon"></i>');
                        $input.after($icon);
                        // Make icon clickable to open datepicker
                        $icon.on('click', function() {
                            $input.focus().datepicker('show');
                        });
                        $input.addClass('date-wrapped');
                    }
                });
            }

            // Function to add icon wrapper to time inputs
            function wrapTimeInputs() {
                $('input[type="time"]').not('.time-wrapped').each(function() {
                    var $input = $(this);
                    if (!$input.parent().hasClass('time-input-wrapper')) {
                        $input.wrap('<div class="time-input-wrapper"></div>');
                        var $icon = $('<i class="mdi mdi-clock-outline time-icon"></i>');
                        $input.after($icon);
                        // Make icon clickable to open timepicker
                        $icon.on('click', function() {
                            $input.focus();
                            if ($input.data('timepicker')) {
                                $input.timepicker('showWidget');
                            }
                        });
                        $input.addClass('time-wrapped');
                    }
                });
            }

            // Function to initialize datepickers
            function initDatepickers() {
                // Wrap date inputs with icon first
                wrapDateInputs();

                // Initialize Bootstrap Datepicker for all date inputs that haven't been initialized
                // Use a more specific selector to avoid conflicts
                $('.date-input-wrapper input[type="date"], input[type="date"]').not('.datepicker-initialized').each(function() {
                    var $input = $(this);
                    var currentValue = $input.val();
                    var inputId = $input.attr('id');
                    
                    // Skip if already wrapped and converted
                    if ($input.attr('type') === 'text' && $input.hasClass('datepicker')) {
                        return;
                    }
                    
                    // Mark as initialized
                    $input.addClass('datepicker-initialized');
                    
                    // Convert to datepicker
                    $input.attr('type', 'text').addClass('datepicker');
                    
                    // Default datepicker options
                    var datepickerOptions = {
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        orientation: 'bottom auto',
                        clearBtn: true
                    };
                    
                    // Special handling for date ranges
                    // Contract end date should be after start date
                    if (inputId === 'contract_end_date' || inputId === 'new_end_date') {
                        var startDateId = inputId === 'contract_end_date' ? 'contract_start_date' : 'new_start_date';
                        var $startDate = $('#' + startDateId);
                        if ($startDate.length && $startDate.val()) {
                            try {
                                datepickerOptions.startDate = new Date($startDate.val());
                            } catch(e) {
                                // Invalid date, ignore
                            }
                        }
                    }
                    
                    // To date should be after from date
                    if (inputId === 'to_date' || inputId === 'filter_to_date') {
                        var fromDateId = inputId === 'to_date' ? 'from_date' : 'filter_from_date';
                        var $fromDate = $('#' + fromDateId);
                        if ($fromDate.length) {
                            // Check if from date has a value
                            var fromDateVal = $fromDate.val();
                            if (fromDateVal) {
                                try {
                                    datepickerOptions.startDate = new Date(fromDateVal);
                                } catch(e) {
                                    // Invalid date, ignore
                                }
                            }
                            
                            // Also listen for changes to from date (in case it's set after to date is initialized)
                            $fromDate.on('changeDate.updateToDate change.updateToDate', function() {
                                var newFromDateVal = $fromDate.val();
                                if (newFromDateVal) {
                                    try {
                                        var newFromDate = new Date(newFromDateVal);
                                        $input.datepicker('setStartDate', newFromDate);
                                        // If to date is before from date, clear it
                                        var toDateVal = $input.val();
                                        if (toDateVal) {
                                            var toDate = $input.datepicker('getDate');
                                            if (toDate && toDate < newFromDate) {
                                                $input.datepicker('setDate', null);
                                            }
                                        }
                                    } catch(e) {
                                        // Invalid date, ignore
                                    }
                                }
                            });
                        }
                    }
                    
                    // Initialize datepicker
                    $input.datepicker(datepickerOptions);
                    
                    // Set current value if exists
                    if (currentValue) {
                        try {
                            $input.datepicker('setDate', currentValue);
                        } catch(e) {
                            // Invalid date, ignore
                        }
                    }
                    
                    // Update end date min date when start date changes
                    if (inputId === 'contract_start_date' || inputId === 'new_start_date' || inputId === 'from_date' || inputId === 'filter_from_date') {
                        // Remove any existing handlers to avoid duplicates
                        $input.off('changeDate.updateEndDate change.updateEndDate');
                        
                        $input.on('changeDate.updateEndDate', function(e) {
                            var endDateId = inputId === 'contract_start_date' ? 'contract_end_date' : 
                                          (inputId === 'new_start_date' ? 'new_end_date' : 
                                          (inputId === 'from_date' ? 'to_date' : 'filter_to_date'));
                            var $endDate = $('#' + endDateId);
                            if ($endDate.length) {
                                // Update startDate for end date picker
                                if ($endDate.data('datepicker')) {
                                    $endDate.datepicker('setStartDate', e.date);
                                    // If end date is before start date, clear it
                                    var endDateVal = $endDate.val();
                                    if (endDateVal) {
                                        var endDateObj = $endDate.datepicker('getDate');
                                        if (endDateObj && endDateObj < e.date) {
                                            $endDate.datepicker('setDate', null);
                                        }
                                    }
                                }
                            }
                        });
                        
                        // Also listen to change event for manual input
                        $input.on('change.updateEndDate', function() {
                            var endDateId = inputId === 'contract_start_date' ? 'contract_end_date' : 
                                          (inputId === 'new_start_date' ? 'new_end_date' : 
                                          (inputId === 'from_date' ? 'to_date' : 'filter_to_date'));
                            var $endDate = $('#' + endDateId);
                            if ($endDate.length && $endDate.data('datepicker')) {
                                var startDateVal = $input.val();
                                if (startDateVal) {
                                    try {
                                        var startDate = new Date(startDateVal);
                                        $endDate.datepicker('setStartDate', startDate);
                                        // Check if end date needs to be cleared
                                        var endDateVal = $endDate.val();
                                        if (endDateVal) {
                                            var endDate = $endDate.datepicker('getDate');
                                            if (endDate && endDate < startDate) {
                                                $endDate.datepicker('setDate', null);
                                            }
                                        }
                                    } catch(e) {
                                        // Invalid date, ignore
                                    }
                                }
                            }
                        });
                    }
                });

                // Initialize month picker for month inputs that haven't been initialized
                $('input[type="month"]').not('.monthpicker-initialized').each(function() {
                    var $input = $(this);
                    var currentValue = $input.val();
                    
                    // Wrap with icon if not already wrapped
                    if (!$input.parent().hasClass('date-input-wrapper')) {
                        $input.wrap('<div class="date-input-wrapper"></div>');
                        $input.after('<i class="mdi mdi-calendar date-icon"></i>');
                    }
                    
                    // Mark as initialized
                    $input.addClass('monthpicker-initialized');
                    
                    // Convert to text input with datepicker
                    $input.attr('type', 'text').addClass('monthpicker');
                    
                    // Initialize datepicker with month view
                    $input.datepicker({
                        format: 'yyyy-mm',
                        viewMode: 'months',
                        minViewMode: 'months',
                        autoclose: true,
                        todayHighlight: true,
                        orientation: 'bottom auto',
                        clearBtn: true
                    });
                    
                    // Set current value if exists
                    if (currentValue) {
                        $input.datepicker('setDate', currentValue);
                    }
                });
            }

            // Function to initialize timepickers
            function initTimepickers() {
                // Wrap time inputs with icon first
                wrapTimeInputs();

                // Initialize Bootstrap Timepicker for all time inputs
                $('.time-input-wrapper input[type="time"], input[type="time"]').not('.timepicker-initialized').each(function() {
                    var $input = $(this);
                    var currentValue = $input.val();
                    var inputId = $input.attr('id');
                    
                    // Skip if already converted
                    if ($input.attr('type') === 'text' && $input.hasClass('timepicker')) {
                        return;
                    }
                    
                    // Mark as initialized
                    $input.addClass('timepicker-initialized');
                    
                    // Convert to text input
                    $input.attr('type', 'text').addClass('timepicker');
                    
                    // Initialize timepicker with 12-hour format
                    $input.timepicker({
                        showMeridian: true,
                        defaultTime: false,
                        minuteStep: 1,
                        showSeconds: false,
                        template: 'dropdown',
                        modalBackdrop: false,
                        icons: {
                            up: 'mdi mdi-chevron-up',
                            down: 'mdi mdi-chevron-down'
                        }
                    });
                    
                    // Set current value if exists - convert 24-hour to 12-hour format
                    if (currentValue) {
                        try {
                            var timeValue = currentValue;
                            // Convert 24-hour format (HH:mm or HH:mm:ss) to 12-hour format
                            if (timeValue.match(/^\d{2}:\d{2}/)) {
                                var parts = timeValue.split(':');
                                var hours = parseInt(parts[0]);
                                var minutes = parts[1];
                                var ampm = hours >= 12 ? 'PM' : 'AM';
                                hours = hours % 12;
                                hours = hours ? hours : 12; // 0 should be 12
                                var formattedTime = (hours < 10 ? '0' : '') + hours + ':' + minutes + ' ' + ampm;
                                $input.timepicker('setTime', formattedTime);
                            } else {
                                $input.timepicker('setTime', timeValue);
                            }
                        } catch(e) {
                            // Invalid time, set manually
                            $input.val(currentValue);
                        }
                    }
                    
                    // Convert 12-hour format back to 24-hour format on change for form submission
                    $input.on('changeTime.timepicker change', function() {
                        var time = $input.val();
                        if (time) {
                            // If time contains AM/PM, convert to 24-hour format for hidden input
                            var match = time.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/i);
                            if (match) {
                                var hours = parseInt(match[1]);
                                var minutes = match[2];
                                var ampm = match[3].toUpperCase();
                                
                                if (ampm === 'PM' && hours !== 12) {
                                    hours += 12;
                                } else if (ampm === 'AM' && hours === 12) {
                                    hours = 0;
                                }
                                
                                // Store 24-hour format in a data attribute for form submission
                                var formatted24 = (hours < 10 ? '0' : '') + hours + ':' + minutes;
                                $input.data('time24', formatted24);
                            }
                        }
                    });
                    
                    // For check-out, ensure it's after check-in
                    if (inputId === 'check_out') {
                        var $checkIn = $('#check_in');
                        if ($checkIn.length) {
                            // Remove existing handlers to avoid duplicates
                            $checkIn.off('changeTime.updateCheckOut change.updateCheckOut');
                            
                            $checkIn.on('changeTime.updateCheckOut change.updateCheckOut', function() {
                                var checkInTime = $checkIn.val();
                                if (checkInTime) {
                                    // Note: Bootstrap Timepicker doesn't have built-in min time validation
                                    // Validation will be handled on form submit in the backend
                                }
                            });
                        }
                    }
                });
            }

            // Initialize datepickers and timepickers on page load
            initDatepickers();
            initTimepickers();

            // Reinitialize datepickers and timepickers when modals are shown (for dynamically added content)
            $(document).on('shown.bs.modal', '.modal', function() {
                setTimeout(function() {
                    initDatepickers();
                    initTimepickers();
                }, 100);
            });

            // Reinitialize datepickers and timepickers after AJAX content is loaded
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    initDatepickers();
                    initTimepickers();
                }, 100);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>

