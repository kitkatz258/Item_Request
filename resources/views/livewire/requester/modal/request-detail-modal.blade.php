@if($showModal && $selectedRequest)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request #{{ $selectedRequest->id }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <h6>Items Requested</h6>
                    <table class="table table-sm">
                        <tbody>
                            @foreach($selectedRequest->requestItems as $ri)
                                <tr wire:key="ri-{{ $ri->id }}">
                                    <td>{{ $ri->item->name }}</td>
                                    <td class="text-muted">x{{ $ri->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h6 class="mt-4">Approval Progress</h6>
                    <ul class="list-group">
                        @foreach($selectedRequest->requestApprovals as $approval)
                            <li class="list-group-item d-flex justify-content-between align-items-start" wire:key="appr-{{ $approval->id }}">
                                <div>
                                    <strong>Step {{ $approval->sequence }}: {{ $approval->approvalLevel->label }}</strong>
                                    @if($approval->remarks)
                                        <div class="text-muted small">Remarks: {{ $approval->remarks }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $approval->status === 'Approved' ? 'success' : ($approval->status === 'Declined' ? 'danger' : ($approval->status === 'Cancelled' ? 'secondary' : 'warning')) }}">
                                    {{ $approval->status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif