<button class="btn waves-effect waves-light btn-sm btn-info view-claim" data-id="{{ $claim->id }}" data-toggle="tooltip" data-placement="top" title="View">
    <i class="ti-eye"></i>
</button>
@if($claim->status == 'pending')
    <button class="btn waves-effect waves-light btn-sm btn-success approve-claim-btn" data-id="{{ $claim->id }}" data-toggle="tooltip" data-placement="top" title="Approve">
        <i class="ti-check"></i>
    </button>
    <button class="btn waves-effect waves-light btn-sm btn-danger reject-claim-btn" data-id="{{ $claim->id }}" data-toggle="tooltip" data-placement="top" title="Reject">
        <i class="ti-close"></i>
    </button>
@endif
