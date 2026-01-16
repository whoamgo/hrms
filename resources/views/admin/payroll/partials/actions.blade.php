<button class="btn waves-effect waves-light btn-sm btn-info view-payslip" data-id="{{ $payroll->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="View">
    <i class="ti-eye"></i>
</button>

<!-- <button class="btn waves-effect waves-light btn-sm btn-primary edit-payslip" data-id="{{ $payroll->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="Edit">
    <i class="ti-pencil"></i>
</button>
 -->
<button class="btn waves-effect waves-light btn-sm btn-success download-payslip-pdf" data-id="{{ $payroll->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="Download PDF">
    <i class="ti-download"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-danger delete-payslip" data-id="{{ $payroll->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="Delete">
    <i class="ti-trash"></i>
</button>


