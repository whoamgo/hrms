<button class="btn waves-effect waves-light btn-sm btn-info edit-department" data-id="{{ $department->id }}" data-toggle="tooltip" data-placement="top" title="Edit">
    <i class="ti-pencil"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-{{ $department->is_active ? 'warning' : 'success' }} toggle-department-status" data-id="{{ $department->id }}" data-toggle="tooltip" data-placement="top" title="{{ $department->is_active ? 'Deactivate' : 'Activate' }}">
    <i class="ti-{{ $department->is_active ? 'close' : 'check' }}"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-danger delete-department" data-id="{{ $department->id }}" data-toggle="tooltip" data-placement="top" title="Delete">
    <i class="ti-trash"></i>
</button>

