@extends('layouts.app')

@section('title', 'Contract Renewal Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Contract Renewal Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="contract-renewals-table" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Employee Name</th>
                            <th>Contract Start Date</th>
                            <th>Contract End Date</th>
                            <th>Days Remaining</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Renew Contract Modal -->
<div class="modal fade Renew" id="renewModal" tabindex="-1" role="dialog" aria-labelledby="renewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Renew Contract</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form id="renew-contract-form">
                    <input type="hidden" id="renew_employee_id" name="employee_id">
                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <label>New Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="new_start_date" name="new_start_date" required>
                                <span class="text-danger error-text new_start_date_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <label>New End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="new_end_date" name="new_end_date" required>
                                <span class="text-danger error-text new_end_date_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                                <span class="text-danger error-text remarks_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
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
</div>

<!-- View Contract History Modal -->
<div class="modal fade" id="viewContractHistoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contract History</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contractHistoryBody">
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
    var table = $('#contract-renewals-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("hr.contract-renewals.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'contract_start_date', name: 'contract_start_date' },
            { data: 'contract_end_date', name: 'contract_end_date' },
            { data: 'days_remaining', name: 'days_remaining' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    // View contract history
    $(document).on('click', '.view-contract-history', function() {
        var employeeId = $(this).data('id');
        $('#viewContractHistoryModal').modal('show');
        $('#contractHistoryBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
        $.ajax({
            url: '{{ url("/hr/contract-renewals") }}/' + employeeId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var html = '<h6>Employee: ' + response.employee.full_name + '</h6>';
                    html += '<h6>Employee ID: ' + response.employee.employee_id + '</h6><hr>';
                    html += '<h6 class="mb-3">Contract History:</h6>';
                    if (response.contract_history && response.contract_history.length > 0) {
                        html += '<table class="table table-bordered">';
                        html += '<thead><tr><th>Start Date</th><th>End Date</th><th>Status</th><th>Remarks</th><th>Renewed At</th></tr></thead>';
                        html += '<tbody>';
                        response.contract_history.forEach(function(contract) {
                            html += '<tr>';
                            html += '<td>' + contract.start_date + '</td>';
                            html += '<td>' + contract.end_date + '</td>';
                            html += '<td><span class="badge badge-' + (contract.status === 'Active' ? 'success' : (contract.status === 'Expired' ? 'danger' : 'secondary')) + '">' + contract.status + '</span></td>';
                            html += '<td>' + (contract.remarks || 'N/A') + '</td>';
                            html += '<td>' + contract.renewed_at + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p class="text-muted">No contract history available.</p>';
                    }
                    $('#contractHistoryBody').html(html);
                }
            },
            error: function() {
                $('#contractHistoryBody').html('<div class="alert alert-danger">Error loading contract history.</div>');
            }
        });
    });

    // Renew contract
    $(document).on('click', '.renew-contract', function() {
        var employeeId = $(this).data('id');
        $('#renew_employee_id').val(employeeId);
        $('#renewModal').modal('show');
    });

    // Close contract
    $(document).on('click', '.close-contract', function() {
        var employeeId = $(this).data('id');

        var url = "{{ route('hr.contract-renewals.close', ':id') }}";   
        var url = url.replace(':id', employeeId);


        if (confirm('Are you sure you want to close this contract?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        table.ajax.reload();
                    }
                }
            });
        }
    });

    // Renew contract form submission
    $('#renew-contract-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var spinner = submitBtn.find('.spinner-border');
        
        spinner.show();
        submitBtn.prop('disabled', true);

        var employeeId = $('#renew_employee_id').val();

        var url = "{{ route('hr.contract-renewals.renew', ':id') }}";   
        var url = url.replace(':id', employeeId);





        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#renewModal').modal('hide');
                    form[0].reset();
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('.' + key.replace('.', '_') + '_error').text(value[0]);
                    });
                }
            },
            complete: function() {
                spinner.hide();
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush


