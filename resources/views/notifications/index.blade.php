@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Notifications</h4>
        </div>
    </div>
</div>
<!-- end page title --> 

@include('components.messages')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">All Notifications</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-sm btn-primary" id="mark-all-read-btn">
                            <i class="mdi mdi-check-all"></i> Mark All as Read
                        </button>
                    </div>
                </div>

                @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Type</th>
                                    <th width="50%">Message</th>
                                    <th width="20%">Date</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                    <tr class="{{ $notification->read_at ? '' : 'table-info' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @php
                                                $type = $notification->data['type'] ?? 'info';
                                                $badgeClass = $type == 'success' ? 'badge-success' : ($type == 'error' ? 'badge-danger' : ($type == 'warning' ? 'badge-warning' : 'badge-info'));
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($type) }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong><br>
                                            <small class="text-muted">{{ $notification->data['message'] ?? '' }}</small>
                                        </td>
                                        <td>{{ $notification->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            @if(!$notification->read_at)
                                                <button type="button" class="btn btn-sm btn-outline-primary mark-read-btn" data-id="{{ $notification->id }}">
                                                    <i class="mdi mdi-check"></i> Mark Read
                                                </button>
                                            @else
                                                <span class="badge badge-secondary">Read</span>
                                            @endif
                                            @if(isset($notification->data['url']) && $notification->data['url'])
                                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-info">
                                                    <i class="mdi mdi-open-in-new"></i> View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="mdi mdi-bell-off-outline" style="font-size: 64px; color: #6c757d;"></i>
                        <h5 class="mt-3">No Notifications</h5>
                        <p class="text-muted">You don't have any notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mark single notification as read
    $(document).on('click', '.mark-read-btn', function() {
        var btn = $(this);
        var notificationId = btn.data('id');

        var url = "{{ route('notifications.read', ':id') }}";   
        var url = url.replace(':id', notificationId);

        
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    btn.closest('tr').removeClass('table-info');
                    btn.replaceWith('<span class="badge badge-secondary">Read</span>');
                    // Reload notification count
                    loadNotificationsCount();
                }
            },
            error: function() {
                alert('Error marking notification as read.');
            }
        });
    });

    // Mark all notifications as read
    $('#mark-all-read-btn').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true);

       var url = "{{ route('notifications.mark-all-read') }}";   
         
        $.ajax({
            url:url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error marking all notifications as read.');
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush

