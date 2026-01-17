@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit User</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="user-form" action="{{ route('admin.users.update', $user) }}" method="POST" data-ajax-form enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                                <span class="text-danger error-text username_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">Role {{$user->role_id}}<span class="text-danger">*</span></label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Select Role </option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_id_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted">Leave blank to keep current password</small>
                                <span class="text-danger error-text password_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <span class="text-danger error-text password_confirmation_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <span class="text-danger error-text status_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="avatar">Avatar</label>
                                @if($user->avatar)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="avatar" name="avatar" accept="image/*">
                                <small class="form-text text-muted">Max size: 2MB. Allowed: jpeg, png, jpg, gif</small>
                                <span class="text-danger error-text avatar_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                            Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

