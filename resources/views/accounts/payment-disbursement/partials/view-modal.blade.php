<div class="modal fade" id="viewDisbursementModal" tabindex="-1" role="dialog" aria-labelledby="viewDisbursementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDisbursementModalLabel">Payment Disbursement Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Employee Name:</label>
                            <p>{{ $disbursement->employee->full_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Employee ID:</label>
                            <p>{{ $disbursement->employee->employee_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Amount:</label>
                            <p>₹{{ number_format($disbursement->amount, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Transaction ID:</label>
                            <p>{{ $disbursement->transaction_id }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Month/Year:</label>
                            <p>{{ $disbursement->month }} {{ $disbursement->year }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Disbursement Status:</label>
                            <p>
                                @if($disbursement->disbursement_status === 'Success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($disbursement->disbursement_status === 'Failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Bank Account Number:</label>
                            <p>{{ $disbursement->employee->bank_account_number ? 'XXXXXX' . substr($disbursement->employee->bank_account_number, -4) : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Bank Name:</label>
                            <p>{{ $disbursement->employee->bank_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">IFSC Code:</label>
                            <p>{{ $disbursement->employee->ifsc_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Created By:</label>
                            <p>{{ $disbursement->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Created At:</label>
                            <p>{{ $disbursement->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    @if($disbursement->remarks)
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Remarks:</label>
                            <p>{{ $disbursement->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

