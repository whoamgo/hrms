<button type="button" class="btn waves-effect waves-light btn-sm btn-info view-employee" data-id="{{ $employee->getRouteKey() }}" title="View">
    <i class="fas fa-eye"></i>
</button>
<a href="{{ route('hr.employees.edit', $employee) }}" class="btn waves-effect waves-light btn-sm btn-primary" title="Edit">
    <i class="mdi mdi-pencil"></i>
</a>
<button type="button" class="btn waves-effect waves-light btn-sm btn-danger delete-employee" data-id="{{ $employee->getRouteKey() }}" data-name="{{ $employee->full_name }}" title="Delete">
    <i class="mdi mdi-trash-can-outline"></i>
</button>

