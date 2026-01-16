@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Profile</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" data-ajax-form>
                @csrf
                <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            <span class="text-danger error-text name_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                            <span class="text-danger error-text username_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                            <span class="text-danger error-text email_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="{{ $user->role ? $user->role->name : 'No Role' }}" readonly>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Avatar</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <span class="text-danger error-text avatar_error"></span>
                            @if($user->avatar)
                                <small class="text-muted">Current: <a href="{{ asset('storage/' . $user->avatar) }}" target="_blank">View</a></small>
                            @endif
                        </div>
                    </div>
                    @if($user->employee)
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Employee ID</label>
                            <input type="text" class="form-control" value="{{ $user->employee->employee_id }}" readonly>
                        </div>
                    </div>
                    @endif
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Last Login</label>
                            <input type="text" class="form-control" value="{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i:s') : 'Never' }}" readonly>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="form-group">
                            <label>Current Login</label>
                            <input type="text" class="form-control" value="{{ $user->current_login_at ? $user->current_login_at->format('d M Y H:i:s') : 'Never' }}" readonly>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Update Profile
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


