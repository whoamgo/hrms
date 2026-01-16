<div class="row">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">Employee Information</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Employee ID</th>
                <td><strong>{{ $employee->employee_id }}</strong></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td>{{ $employee->full_name }}</td>
            </tr>
            <tr>
                <th>Employee Type</th>
                <td><span class="badge badge-info">{{ $employee->employee_type }}</span></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($employee->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">Bank Information</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 30%;">Account Holder Name</th>
                <td><strong>{{ $employee->account_holder_name ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <th>Bank Account Number</th>
                <td>{{ $employee->bank_account_number ?? 'N/A' }}</td>
            </tr>
         <tr>
                <th>Bank Name</th>
                <td>{{ $employee->bank_name ?? 'N/A' }}</td>
            </tr>
         <tr>
                <th>Bank Branch Name</th>
                <td>{{ $employee->bank_branch_name ?? 'N/A' }}</td>
            </tr>
         <tr>
                <th>IFSC Code</th>
                <td>{{ $employee->ifsc_code ?? 'N/A' }}</td>
            </tr>
         <tr>
                <th>PAN Card Number</th>
                <td>{{ $employee->pan_card_number ?? 'N/A' }}</td>
            </tr>
      
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <h6 class="text-primary mb-3">Personal Details</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 40%;">Father/Mother Name</th>
                <td>{{ $employee->father_mother_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td>{{ $employee->dob ? $employee->dob->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $employee->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Mobile Number</th>
                <td>{{ $employee->mobile_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $employee->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $employee->address ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary mb-3">Official Details</h6>
        <table class="table table-bordered">
            <tr>
                <th style="width: 40%;">Department</th>
                <td>{{ $employee->department ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $employee->designation ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date of Joining</th>
                <td>{{ $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : 'N/A' }}</td>
            </tr>
          <!--   <tr>
                <th>Employment Status</th>
                <td>{{ $employee->employment_status ?? 'N/A' }}</td>
            </tr> -->
            @if($employee->employee_type == 'Contract')
            <tr>
                <th>Contract Start Date</th>
                <td>{{ $employee->contract_start_date ? $employee->contract_start_date->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Contract End Date</th>
                <td>{{ $employee->contract_end_date ? $employee->contract_end_date->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            @endif
            <!-- <tr>
                <th>Linked User</th>
                <td>
                    @if($employee->user)
                        {{ $employee->user->name }} ({{ $employee->user->email }})
                    @else
                        <span class="text-muted">Not linked</span>
                    @endif
                </td>
            </tr> -->
        </table>
    </div>
</div>

@if($employee->appointment_letter || $employee->id_proof)
<div class="row mt-3">
    <div class="col-md-12">
        <h6 class="text-primary mb-3">Documents</h6>
        <table class="table table-bordered">
            @if($employee->appointment_letter)
            <tr>
                <th style="width: 30%;">Appointment Letter</th>
                <td>
                    <a href="{{ asset('storage/' . $employee->appointment_letter) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="mdi mdi-file"></i> View Document
                    </a>
                </td>
            </tr>
            @endif
            @if($employee->id_proof)
            <tr>
                <th>ID Proof</th>
                <td>
                    <a href="{{ asset('storage/' . $employee->id_proof) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="mdi mdi-file"></i> View Document
                    </a>
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-md-12">
        <small class="text-muted">
            Created: {{ $employee->created_at->format('Y-m-d H:i:s') }} | 
            Updated: {{ $employee->updated_at->format('Y-m-d H:i:s') }}
        </small>
    </div>
</div>

