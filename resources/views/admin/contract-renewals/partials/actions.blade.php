<button class="btn waves-effect waves-light btn-sm btn-info view-contract-history" data-id="{{ $employee->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="View Contract History">
    <i class="ti-eye"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-success renew-contract" data-id="{{ $employee->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="Renew Contract">
    <i class="mdi mdi-autorenew"></i>
</button>
<button class="btn waves-effect waves-light btn-sm btn-danger close-contract" data-id="{{ $employee->getRouteKey() }}" data-toggle="tooltip" data-placement="top" title="Close Contract">
    <i class="fas fa-window-close"></i>
</button>


