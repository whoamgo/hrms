@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Dashboard</h4>
        </div>
    </div>
</div>
<!-- end page title --> 

<div class="row">

    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box2">
            <i class="icon-people float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Total Employees</h6>
            <h3 class="my-3">
                <span data-plugin="counterup">{{ $stats['total_employees'] ?? 0 }}</span>
            </h3>
            <span class="badge badge-success mr-1">Active</span> 
            <span class="text-muted">Currently active employees</span>
        </div>
    </div>

    
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box2">
            <i class="icon-layers float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Active Contracts</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['active_contracts'] ?? 0 }}</span></h3>
            @if(($stats['expiring_contracts'] ?? 0) > 0)
                <span class="badge badge-warning mr-1">{{ $stats['expiring_contracts'] }} Expiring</span>
            @else
                <span class="badge badge-success mr-1">All Active</span>
            @endif
            <span class="text-muted">Contract employees</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box3">
            <i class="icon-docs float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Pending Leaves</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['pending_leaves'] ?? 0 }}</span></h3>
            <span class="badge badge-warning mr-1">Awaiting</span> 
            <span class="text-muted">Requires approval</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box4">
            <i class="icon-social-dribbble float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Payroll Generated</h6>
            <h3 class="my-3" data-plugin="counterup">{{ $stats['payroll_generated'] ?? 0 }}</h3>
            <span class="badge badge-info mr-1">{{ date('F Y') }}</span> 
            <span class="text-muted">Current month</span>
        </div>
    </div>
</div>

<div class="row mt-3" style="display:none;">
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box1">
            <i class="icon-user float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Total Users</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['total_users'] ?? 0 }}</span></h3>
            <span class="badge badge-success mr-1">Active</span> 
            <span class="text-muted">System users</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box2">
            <i class="icon-wallet float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Pending TA/DA</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['pending_tada_claims'] ?? 0 }}</span></h3>
            <span class="badge badge-warning mr-1">Pending</span> 
            <span class="text-muted">Claims awaiting approval</span>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard.init.js') }}"></script>
@endpush

