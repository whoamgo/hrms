<div class="row">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">TA/DA Claim Information</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Employee</th>
                <td>{{ $tadaClaim->employee->full_name }} ({{ $tadaClaim->employee->employee_id }})</td>
            </tr>
            <tr>
                <th>Travel Date</th>
                <td>{{ $tadaClaim->travel_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Purpose</th>
                <td>{{ $tadaClaim->purpose }}</td>
            </tr>
            <tr>
                <th>Distance</th>
                <td>{{ $tadaClaim->distance ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Amount Claimed</th>
                <td>₹{{ number_format($tadaClaim->amount_claimed, 2) }}</td>
            </tr>
            <tr>
                <th>Bill File</th>
                <td>
                    @if($tadaClaim->bill_file)
                        <a href="{{ Storage::url($tadaClaim->bill_file) }}" target="_blank">{{ basename($tadaClaim->bill_file) }}</a>
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($tadaClaim->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($tadaClaim->status == 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
            </tr>
            @if($tadaClaim->rejection_reason)
            <tr>
                <th>Rejection Reason</th>
                <td>{{ $tadaClaim->rejection_reason }}</td>
            </tr>
            @endif
            @if($tadaClaim->approver)
            <tr>
                <th>Approved By</th>
                <td>{{ $tadaClaim->approver->name }} on {{ $tadaClaim->approved_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>
<!-- 
@if($tadaClaim->status == 'pending')
<div class="row mt-3">
    <div class="col-md-12">
        <button class="btn btn-success approve-claim-btn" data-id="{{ $tadaClaim->getRouteKey() }}">Approve</button>
        <button class="btn btn-danger reject-claim-btn" data-id="{{ $tadaClaim->getRouteKey() }}">Reject</button>
    </div>
</div>
@endif -->

<script>
$(document).ready(function() {
    $('.approve-claim-btn').on('click', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to approve this claim?')) {

            var url = "{{ route('admin.tada-claims.approve', ':id') }}";   
            var url = url.replace(':id', id);


            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                       // alert(response.message);
                        $('#viewModal').modal('hide');
                        $('#tada-claims-table').DataTable().ajax.reload();
                    }
                }
            });
        }
    });

    $('.reject-claim-btn').on('click', function() {
        var id = $(this).data('id');
        var reason = prompt('Please enter rejection reason:');
        if (reason) {

            var url = "{{ route('admin.tada-claims.reject', ':id') }}";   
            var url = url.replace(':id', id);



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
                       // alert(response.message);
                        $('#viewModal').modal('hide');
                        $('#tada-claims-table').DataTable().ajax.reload();
                    }
                }
            });
        }
    });
});
</script>


