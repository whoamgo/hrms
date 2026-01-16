<button type="button" class="btn waves-effect waves-light btn-sm btn-info view-service-record" data-id="{{ $record->id }}" title="View">
    <i class="fas fa-eye"></i>
</button>
<a href="{{ route('admin.service-records.edit', $record) }}" class="btn waves-effect waves-light btn-sm btn-primary" title="Edit">
    <i class="mdi mdi-pencil"></i>
</a>
<button type="button" class="btn waves-effect waves-light btn-sm btn-danger delete-service-record" data-id="{{ $record->id }}" title="Delete">
    <i class="mdi mdi-trash-can-outline"></i>
</button>

