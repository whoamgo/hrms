@extends('layouts.app')

@section('title', 'Employee Dashboard')

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

@include('components.messages')

@if(!isset($employee) || !$employee)
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Notice:</strong> Your employee profile has not been created yet. Please contact your HR department to set up your employee profile.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box2">
            <i class="icon-layers float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Leave Balance</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['leave_balance'] ?? 0 }}</span></h3>
            <span class="badge badge-danger mr-1"> -29% </span> 
            <span class="text-muted">From previous period</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box3">
            <i class="icon-docs float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Latest Payslip</h6>
            <h3 class="my-3"><span data-plugin="counterup">{{ $stats['latest_payslip'] ?? 0 }}</span></h3>
            <span class="badge badge-pink mr-1"> 0% </span> 
            <span class="text-muted">From previous period</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card-box tilebox-one box4">
            <i class="icon-social-dribbble float-right m-0 h2 text-muted"></i>
            <h6 class="text-muted text-uppercase mt-0">Pending TA/DA Claims</h6>
            <h3 class="my-3" data-plugin="counterup">{{ $stats['pending_tada_claims'] ?? 0 }}</h3>
            <span class="badge badge-warning mr-1"> +89% </span> 
            <span class="text-muted">Current Month</span>
        </div>
    </div>
</div>
<!-- end row -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard.init.js') }}"></script>
@endpush

