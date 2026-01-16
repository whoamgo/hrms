@extends('layouts.app')

@section('title', 'View Role')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">View Role: {{ $role->name }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Role Details</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td>{{ $role->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td>{{ $role->slug }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $role->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($role->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Statistics</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Total Users</th>
                        <td><span class="badge badge-info">{{ $role->users->count() }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Permissions</th>
                        <td><span class="badge badge-success">{{ $role->permissions->count() }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Menu Items</th>
                        <td><span class="badge badge-warning">{{ $role->menuItems->count() }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Permissions</h5>
                @if($role->permissions->count() > 0)
                    <ul class="list-group">
                        @foreach($role->permissions as $permission)
                            <li class="list-group-item">
                                <strong>{{ $permission->name }}</strong>
                                @if($permission->module)
                                    <span class="badge badge-secondary float-right">{{ $permission->module }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No permissions assigned.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Menu Items</h5>
                @if($role->menuItems->count() > 0)
                    <ul class="list-group">
                        @foreach($role->menuItems as $menuItem)
                            <li class="list-group-item">
                                <i class="{{ $menuItem->icon }}"></i> {{ $menuItem->title }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No menu items assigned.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Back to List</a>
        <!-- <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">Edit Role</a> -->
    </div>
</div>
@endsection

