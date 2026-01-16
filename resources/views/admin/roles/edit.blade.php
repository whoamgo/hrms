@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Role</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="role-form" action="{{ route('admin.roles.update', $role) }}" method="POST" data-ajax-form>
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="slug" name="slug" value="{{ $role->slug }}" required>
                                <span class="text-danger error-text slug_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $role->description }}</textarea>
                        <span class="text-danger error-text description_error"></span>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $role->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <hr>

                    <h5>Permissions</h5>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-primary" id="check-all-permissions">Check All Permissions</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="uncheck-all-permissions">Uncheck All Permissions</button>
                    </div>
                    <div class="row">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>{{ $module }}</strong>
                                    <button type="button" class="btn btn-xs btn-info toggle-module-permissions" data-module="{{ $module }}">
                                        Check All
                                    </button>
                                </div>
                                <div class="card-body">
                                    @foreach($modulePermissions as $permission)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input permission-checkbox module-{{ $module }}" 
                                               id="permission_{{ $permission->id }}" 
                                               name="permissions[]" 
                                               value="{{ $permission->id }}"
                                               {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <span class="text-danger error-text permissions_error"></span>

                    <hr>

                    <h5>Menu Items</h5>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-primary" id="check-all-menu-items">Check All Menu Items</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="uncheck-all-menu-items">Uncheck All Menu Items</button>
                    </div>
                    <div class="row">
                        @foreach($menuItems as $menuItem)
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input menu-item-checkbox" 
                                       id="menu_{{ $menuItem->id }}" 
                                       name="menu_items[]" 
                                       value="{{ $menuItem->id }}"
                                       {{ $role->menuItems->contains($menuItem->id) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="menu_{{ $menuItem->id }}">
                                    <i class="{{ $menuItem->icon }}"></i> {{ $menuItem->title }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <span class="text-danger error-text menu_items_error"></span>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            Update Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name (only if slug is empty or user is not editing it)
    $('#name').on('keyup', function() {
        if ($('#slug').is(':focus')) {
            return; // Don't auto-update if user is editing slug manually
        }
        var slug = $(this).val().toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Only allow lowercase letters, numbers, spaces, and hyphens
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
            .replace(/^-+|-+$/g, '') // Remove leading/trailing hyphens
            .trim();
        $('#slug').val(slug);
    });

    // Validate slug format on blur
    $('#slug').on('blur', function() {
        var slug = $(this).val().toLowerCase()
            .replace(/[^a-z0-9-]/g, '') // Only allow lowercase letters, numbers, and hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
            .replace(/^-+|-+$/g, '') // Remove leading/trailing hyphens
            .trim();
        $(this).val(slug);
    });

    // Check all permissions
    $('#check-all-permissions').on('click', function() {
        $('.permission-checkbox').prop('checked', true);
    });

    // Uncheck all permissions
    $('#uncheck-all-permissions').on('click', function() {
        $('.permission-checkbox').prop('checked', false);
    });

    // Toggle all permissions in a module
    $('.toggle-module-permissions').on('click', function() {
        var module = $(this).data('module');
        var $checkboxes = $('.module-' + module);
        var allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
        
        if (allChecked) {
            $checkboxes.prop('checked', false);
            $(this).text('Check All');
        } else {
            $checkboxes.prop('checked', true);
            $(this).text('Uncheck All');
        }
    });

    // Check all menu items
    $('#check-all-menu-items').on('click', function() {
        $('.menu-item-checkbox').prop('checked', true);
    });

    // Uncheck all menu items
    $('#uncheck-all-menu-items').on('click', function() {
        $('.menu-item-checkbox').prop('checked', false);
    });
});
</script>
@endpush

