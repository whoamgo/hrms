@extends('layouts.app')

@section('title', 'TA/DA Claim')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box flex_btn">
            <h4 class="page-title">TA/DA Claim</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="filter-form">
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Travel Date</label>
                            <input type="date" class="form-control" id="filter_travel_date" name="travel_date">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="button" class="btn btn-primary" id="search-btn">Search</button>
                            <button type="button" class="btn btn-secondary" id="reset-filters">Reset</button>
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
                            <th>Upload Bills</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">TA/DA Claim Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                Loading...
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
        ajax: {
            url: '{{ route("admin.tada-claims.data") }}',
            data: function(d) {
                d.travel_date = $('#filter_travel_date').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'travel_date', name: 'travel_date' },
            { data: 'purpose', name: 'purpose' },
            { data: 'distance', name: 'distance' },
            { data: 'amount_claimed', name: 'amount_claimed' },
            { data: 'bill_file', name: 'bill_file' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    if (data === 'approved') {
                        return '<span class="badge bg-success">Approved</span>';
                    } else if (data === 'rejected') {
                        return '<span class="badge bg-danger">Rejected</span>';
                    } else {
                        return '<span class="badge bg-warning">Pending</span>';
                    }
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#search-btn').on('click', function() {
        table.ajax.reload();
    });

    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        table.ajax.reload();
    });

    // View modal
    $(document).on('click', '.view-claim', function() {
        var id = $(this).data('id');

        var viewUrl = "{{ route('admin.tada-claims.show', ':id') }}";

        // Replace placeholder with real ID
        viewUrl = viewUrl.replace(':id', id);


        $.ajax({
            url: viewUrl,
            type: 'GET',
            data: { ajax: true },
            success: function(response) {
                $('#viewModalBody').html(response);
                $('#viewModal').modal('show');
            }
        });
    });

    // Approve claim
    $(document).on('click', '.approve-claim-btn', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
       
        var url = "{{ route('admin.tada-claims.approve', ':id') }}";   
        var url = url.replace(':id', id);



        if (confirm('Are you sure you want to approve this claim?')) {



            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        //alert(response.message);
                        $('#viewModal').modal('hide');
                        table.ajax.reload();
                    }
                }
            });
        }
    });

    // Reject claim
    $(document).on('click', '.reject-claim-btn', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        var reason = prompt('Please enter rejection reason:');

        var url = "{{ route('admin.tada-claims.reject', ':id') }}";   
        var url = url.replace(':id', id);

        if (reason) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    rejection_reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        //alert(response.message);
                        $('#viewModal').modal('hide');
                        table.ajax.reload();
                    }
                }
            });
        }
    });
});
</script>
@endpush

