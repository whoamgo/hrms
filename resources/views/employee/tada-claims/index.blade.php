@extends('layouts.app')

@section('title', 'TA/DA Claim')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">TA/DA Claim</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="tada-claim-form" action="{{ route('employee.tada-claims.store') }}" method="POST" data-ajax-form enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Travel Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="travel_date" name="travel_date" required>
                            <span class="text-danger error-text travel_date_error"></span>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Purpose <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="purpose" name="purpose" required>
                            <span class="text-danger error-text purpose_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Distance</label>
                            <input type="text" class="form-control" id="distance" name="distance" placeholder="e.g., 120 Km">
                            <span class="text-danger error-text distance_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Amount Claimed <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="amount_claimed" name="amount_claimed" required>
                            <span class="text-danger error-text amount_claimed_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group file_upload">
                            <label>Upload Bills (Multiple)</label>
                            <input type="file" class="form-control" id="bill_files" name="bill_files[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <small class="form-text text-muted">You can select multiple files at once</small>
                            <span class="text-danger error-text bill_files_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tada-claims-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Travel Date</th>
                            <th>Purpose</th>
                            <th>Distance</th>
                            <th>Amount Claimed</th>
                             <th>Status</th>
                             <th>Rejection Reason</th>
                            <th>Upload Bills</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/datatable.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#tada-claims-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("employee.tada-claims.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'travel_date', name: 'travel_date' },
            { data: 'purpose', name: 'purpose' },
            { data: 'distance', name: 'distance' },
            { data: 'amount_claimed', name: 'amount_claimed' },
            { data: 'status', name: 'status' },
            { data: 'rejection_reason', name: 'rejection_reason' },
            { data: 'bill_file', name: 'bill_file' }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush


