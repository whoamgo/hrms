@extends('layouts.app')

@section('title', 'HR Dashboard')

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
            <h6 class="text-muted text-uppercase mt-0">Pending Leaves</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['pending_leaves'] ?? 0 }}</span></h3>
            <span class="badge badge-warning mr-1">Awaiting</span> 
            <span class="text-muted">Requires approval</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box2">
            <i class="icon-layers float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Today's Attendance</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['today_attendance'] ?? 0 }}</span></h3>
            <span class="badge badge-info mr-1">Today</span> 
            <span class="text-muted">{{ date('d M Y') }}</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box3">
            <i class="icon-docs float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Expiring Contracts</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['expiring_contracts'] ?? 0 }}</span></h3>
            @if(($stats['expired_contracts'] ?? 0) > 0)
                <span class="badge badge-danger mr-1">{{ $stats['expired_contracts'] }} Expired</span>
            @else
                <span class="badge badge-warning mr-1">Next 30 Days</span>
            @endif
            <span class="text-muted">Requires attention</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box4">
            <i class="icon-social-dribbble float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Payroll Generated</h6>
            <h3 class="my-3" data-plugin="counterup">{{ $stats['payroll_pending'] ?? 0 }}</h3>
            <span class="badge badge-info mr-1">{{ date('F Y') }}</span> 
            <span class="text-muted">Current month</span>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard.init.js') }}"></script>
@endpush

