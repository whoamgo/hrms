<button type="button" class="btn waves-effect waves-light btn-sm btn-info view-attendance" data-id="{{ $attendance->id }}" title="View">
    <i class="fas fa-eye"></i>
</button>
<a href="{{ route('hr.attendances.edit', $attendance) }}" class="btn waves-effect waves-light btn-sm btn-primary" title="Edit">
    <i class="mdi mdi-pencil"></i>
</a>
<button type="button" class="btn waves-effect waves-light btn-sm btn-danger delete-attendance" data-id="{{ $attendance->id }}" title="Delete">
    <i class="mdi mdi-trash-can-outline"></i>
</button>

