@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
  

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Activity Logs Management</h4>
        </div>
    </div>
</div>

@php
function actionBadge($action) {
    return match ($action) {
        'created' => 'success',
        'updated' => 'warning',
        'deleted' => 'danger',
        'login'   => 'primary',
        'logout'  => 'secondary',
        default   => 'info',
    };
}
@endphp


<div class="row">
    <div class="col-12">
        <div class="card-box">
                <form method="GET" class="row g-2 mb-3">

                    {{-- USER --}}
                    <div class="col-md-3">
                        <select name="user_id" class="form-control">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ACTION --}}
                    <div class="col-md-2">
                        <select name="action" class="form-control">
                            <option value="">All Actions</option>
                            @foreach (['created','updated','deleted','login','logout'] as $action)
                                <option value="{{ $action }}"
                                    {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               name="from_date"
                               class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    {{-- TO DATE --}}
                    <div class="col-md-2">
                        <input type="date"
                               name="to_date"
                               class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    {{-- BUTTONS --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100">Filter</button>

                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>

                </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
               


                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Description</th>
                            <th>IP</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($activities as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user_name ?? 'System' }}</td>
                                <td>
                                    <span class="badge bg-{{ actionBadge($log->action) }}">
                                        {{ strtoupper($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    {{ class_basename($log->model_type) }}
                                    #{{ $log->model_id }}
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->method }}</td>
                                <td>{{ $log->created_at }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary view-log"
                                        data-log='@json($log)'
                                    >
                                        View
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <!-- <div class="pagination-sm">
                        {{ $activities->links() }}
                    </div> -->
                </div>

 
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL ================= --}}
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Activity Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- BASIC INFO --}}
                <div class="row mb-3">
                    <div class="col-md-4"><strong>User:</strong> <span id="logUser"></span></div>
                    <div class="col-md-4"><strong>Action:</strong> <span id="logAction"></span></div>
                    <div class="col-md-4"><strong>Date:</strong> <span id="logDate"></span></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"><strong>Model:</strong> <span id="logModel"></span></div>
                    <div class="col-md-3"><strong>Method:</strong> <span id="logMethod"></span></div>
                    <div class="col-md-3"><strong>IP:</strong> <span id="logIP"></span></div>
                </div>

                <hr>

                {{-- OLD VALUES --}}
                <div class="mb-3">
                    <button class="btn btn-sm btn-secondary" data-bs-toggle="collapse" data-bs-target="#oldValues">
                        Old Values
                    </button>
                    <div id="oldValues" class="collapse mt-2">
                        <pre class="bg-light p-3 rounded" id="logOldValues"></pre>
                    </div>
                </div>

                {{-- NEW VALUES --}}
                <div class="mb-3">
                    <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#newValues">
                        New Values
                    </button>
                    <div id="newValues" class="collapse show mt-2">
                        <pre class="bg-light p-3 rounded" id="logNewValues"></pre>
                    </div>
                </div>

                {{-- USER AGENT --}}
                <div class="mb-3">
                    <label class="form-label"><strong>User Agent</strong></label>
                    <textarea class="form-control" rows="2" id="logUserAgent" readonly></textarea>
                </div>

                {{-- ROUTE --}}
                <div class="mb-3">
                    <label class="form-label"><strong>Route</strong></label>
                    <input type="text" class="form-control" id="logRoute" readonly>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal">
                    Close
                </button>

            </div>

        </div>
    </div>
</div>

@endsection


  @push('scripts')
<script>

$(document).ready(function () {
    $('select, input[type="date"]').on('change', function () {
        $(this).closest('form').submit()
    })
})


$(document).ready(function () {

    // OPEN MODAL
    $(document).on('click', '.view-log', function () {

        let log = $(this).data('log')

        $('#logUser').text(log.user_name ?? 'System')
        $('#logDate').text(log.created_at)
        $('#logIP').text(log.ip_address ?? '-')
        $('#logMethod').text(log.method ?? '-')
        $('#logRoute').val(log.route ?? '-')
        $('#logUserAgent').val(log.user_agent ?? '-')

        let model = (log.model_type ? log.model_type.split('\\').pop() : '-')
        $('#logModel').text(model + (log.model_id ? ' #' + log.model_id : ''))

        $('#logAction').html(
            `<span class="badge bg-${actionColor(log.action)}">${log.action.toUpperCase()}</span>`
        )

        $('#logOldValues').text(
            log.old_values
                ? JSON.stringify(log.old_values, null, 2)
                : 'No data'
        )

        $('#logNewValues').text(
            log.new_values
                ? JSON.stringify(log.new_values, null, 2)
                : 'No data'
        )

        $('#activityModal').modal('show')
    })

    // CLOSE MODAL (FIX)
    $(document).on('click', '.close-modal', function () {
        $('#activityModal').modal('hide')
    })

    function actionColor(action) {
        switch (action) {
            case 'created': return 'success'
            case 'updated': return 'warning'
            case 'deleted': return 'danger'
            case 'login': return 'primary'
            case 'logout': return 'secondary'
            default: return 'info'
        }
    }

})




 $('#logOldValues').text(
     log.old_values
         ? JSON.stringify(JSON.parse(log.old_values), null, 2)
         : 'No data'
 )

 $('#logNewValues').text(
     log.new_values
         ? JSON.stringify(JSON.parse(log.new_values), null, 2)
         : 'No data'
 )
</script>
@endpush

