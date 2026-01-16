@extends('layouts.app')

@section('title', 'Leave Apply')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Leave Apply</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <form id="leave-form" action="{{ route('employee.leaves.store') }}" method="POST" data-ajax-form>
                @csrf
                
                <div class="row">
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Apply For</label>
                            <div class="d-flex radiobutton_top">
                                <div class="radio_flex">
                                    <input type="radio" id="Leave" name="apply_for" value="Leave" checked>
                                    <label for="Leave">Leave</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="leave_type" name="leave_type" required>
                                <option value="">Select Leave Type</option>
                                <option value="CL">Casual Leave (CL)</option>
                                <option value="SL">Sick Leave (SL)</option>
                                <option value="SPL">Special Leave</option>
                            </select>
                            <span class="text-danger error-text leave_type_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <div class="d-flex radiobutton_top">
                                <div class="radio_flex">
                                    <input type="radio" id="Full" name="day_type" value="Full" checked>
                                    <label for="Full">Full Day</label>
                                </div>
                                <div class="radio_flex">
                                    <input type="radio" id="Half" name="day_type" value="Half">
                                    <label for="Half">Half Day</label>
                                </div>
                            </div>
                            <span class="text-danger error-text day_type_error"></span>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="from_date" name="from_date" required>
                            <span class="text-danger error-text from_date_error"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="to_date" name="to_date" required>
                            <span class="text-danger error-text to_date_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                            <span class="text-danger error-text subject_error"></span>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label>Comments (optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                            <span class="text-danger error-text message_error"></span>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-4">
                        <div class="form-group">
                            <label class="w-100">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Apply Now
                            </button>
                            <button type="button" class="btn btn-danger" onclick="window.location.reload();">Cancel</button>
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
            <h5 class="mb-3">My Leave Status</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="my-leaves-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Leave Type</th>
                            <th>Days</th>
                            <th>ApplyFor</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Reason</th>
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
    var table = $('#my-leaves-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("employee.leaves.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'from_date', name: 'from_date' },
            { data: 'to_date', name: 'to_date' },
            { data: 'leave_type', name: 'leave_type' },
            { data: 'days', name: 'days' },
            { data: 'apply_for', name: 'apply_for' },
            { data: 'subject', name: 'subject' },
            { data: 'message', name: 'message' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    if (data === 'approved') {
                        return '<span class="badge bg-success">Approved</span>';
                    } else if (data === 'rejected') {
                        return '<span class="badge bg-danger">Rejected</span>';
                    } else {
                        return '<span class="badge bg-warning">Pending</span>';
                    }
                }
            },
            { data: 'reason', name: 'reason' }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush


