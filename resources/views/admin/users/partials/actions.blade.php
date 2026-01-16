<!-- <button type="button" class="btn btn-sm btn-info view-user" data-id="{{ $user->id }}" title="View">
    <i class="mdi mdi-eye"></i>
</button> -->
<a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edit">
    <i class="mdi mdi-pencil"></i>
</a>
@if($user->id !== auth()->id())
<button type="button" class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Delete">
    <i class="mdi mdi-delete"></i>
</button>
@endif

