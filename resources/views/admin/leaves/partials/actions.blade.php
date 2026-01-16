@if($leave->status == 'pending')
<button type="button" class="btn waves-effect waves-light btn-sm btn-success approve-leave" data-id="{{ $leave->getRouteKey() }}" title="Approve">
    <i class="fas fa-check-square"></i>
</button>
<button type="button" class="btn waves-effect waves-light btn-sm btn-danger reject-leave" data-id="{{ $leave->getRouteKey() }}" title="Reject">
    <i class="fas fa-window-close"></i>
</button>
@endif
<button type="button" class="btn waves-effect waves-light btn-sm btn-info view-leave" data-id="{{ $leave->getRouteKey() }}" title="View">
    <i class="fas fa-eye"></i>
</button>
<a href="{{ route('admin.leaves.edit', $leave) }}" class="btn waves-effect waves-light btn-sm btn-primary" title="Edit">
    <i class="mdi mdi-pencil"></i>
</a>
<button type="button" class="btn waves-effect waves-light btn-sm btn-danger delete-leave" data-id="{{ $leave->getRouteKey() }}" title="Delete">
    <i class="mdi mdi-trash-can-outline"></i>
</button>

