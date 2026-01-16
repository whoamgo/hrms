<div class="row">
    <div class="col-md-4 text-center mb-3">
        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
        @else
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                <i class="mdi mdi-account" style="font-size: 80px; color: #6c757d;"></i>
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="table table-bordered">
            <tr>
                <th style="width: 40%;">ID</th>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Username</th>
                <td>{{ $user->username }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    @if($user->role)
                        <span class="badge badge-info">{{ $user->role->name }}</span>
                    @else
                        <span class="text-muted">No Role</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($user->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Email Verified</th>
                <td>
                    @if($user->email_verified_at)
                        <span class="badge badge-success">Verified</span>
                        <small class="text-muted">({{ $user->email_verified_at->format('Y-m-d H:i:s') }})</small>
                    @else
                        <span class="badge badge-warning">Not Verified</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>

