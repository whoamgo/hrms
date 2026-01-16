@extends('layouts.app')

@section('title', 'Accounts Dashboard')

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
            <h6 class="text-muted text-uppercase mt-0">Payroll Generated</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['payroll_generated'] ?? 0 }}</span></h3>
            <span class="badge badge-info mr-1">{{ date('F Y') }}</span> 
            <span class="text-muted">Current month</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box4">
            <i class="icon-layers float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Pending Disbursements</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['pending_honorarium'] ?? 0 }}</span></h3>
            <span class="badge badge-warning mr-1">Pending</span> 
            <span class="text-muted">Awaiting processing</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box3">
            <i class="icon-docs float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Pending TA/DA Claims</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['pending_tada_claims'] ?? 0 }}</span></h3>
            <span class="badge badge-warning mr-1">Awaiting</span> 
            <span class="text-muted">Requires approval</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box1">
            <i class="icon-wallet float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Successful Disbursements</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['successful_disbursements'] ?? 0 }}</span></h3>
            <span class="badge badge-success mr-1">Success</span> 
            <span class="text-muted">Current month</span>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard.init.js') }}"></script>
@endpush

