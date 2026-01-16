<button class="btn waves-effect waves-light btn-sm btn-info edit-designation" data-id="{{ $designation->id }}" data-toggle="tooltip" data-placement="top" title="Edit">
    <i class="ti-pencil"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-{{ $designation->is_active ? 'warning' : 'success' }} toggle-designation-status" data-id="{{ $designation->id }}" data-toggle="tooltip" data-placement="top" title="{{ $designation->is_active ? 'Deactivate' : 'Activate' }}">
    <i class="ti-{{ $designation->is_active ? 'close' : 'check' }}"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-danger delete-designation" data-id="{{ $designation->id }}" data-toggle="tooltip" data-placement="top" title="Delete">
    <i class="ti-trash"></i>
</button>

