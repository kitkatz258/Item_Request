@if($showModal && $selectedRequest)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request #{{ $selectedRequest->id }} — {{ $selectedRequest->user->name }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <h6>Item Requested</h6>
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width: 150px;">Approve Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedRequest->requestItems as $ri)
                                @php $current = $approvedQuantities[$ri->id] ?? 0; @endphp
                                <tr wire:key="ri-{{ $ri->id }}">
                                    <td>{{ $ri->item->name }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($current <= 1)
                                                <button type="button" class="btn btn-outline-danger"
                                                    wire:click="decrementApprovedQty({{ $ri->id }})"
                                                    wire:confirm="This will remove {{ $ri->item->name }} from the request{{ $selectedRequest->requestItems->count() === 1 ? ' and decline the entire request since it is the only item' : '' }}. Continue?">
                                                    &minus;
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary"
                                                    wire:click="decrementApprovedQty({{ $ri->id }})">
                                                    &minus;
                                                </button>
                                            @endif
                                            <span class="btn btn-light disabled">{{ $current }}</span>
                                            <button type="button" class="btn btn-outline-secondary"
                                                wire:click="incrementApprovedQty({{ $ri->id }})"
                                                @if($current >= $ri->approved_quantity) disabled @endif>
                                                &plus;
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @php
                        $priorApprovals = $selectedRequest->requestApprovals->where('status', '!=', 'Pending');
                    @endphp
                    @if($priorApprovals->isNotEmpty())
                        <h6 class="mt-4">Approval History</h6>
                        <ul class="list-group mb-3">
                            @foreach($priorApprovals as $approval)
                                <li class="list-group-item" wire:key="hist-{{ $approval->id }}">
                                    <div class="d-flex justify-content-between">
                                        <strong>Step {{ $approval->sequence }}: {{ $approval->approvalLevel->label }}</strong>
                                        <span class="badge bg-{{ $approval->status === 'Approved' ? 'success' : 'danger' }}">
                                            {{ $approval->status }}
                                        </span>
                                    </div>
                                    @if($approval->remarks)
                                        <div class="text-muted small mt-1">"{{ $approval->remarks }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-3">
                        <label class="form-label">Remarks <span class="text-muted">(required if declining)</span></label>
                        <textarea class="form-control" wire:model="remarks" rows="2" placeholder="Add a note, especially if declining..."></textarea>
                        @error('remarks') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="decline">Decline</button>
                    <button type="button" class="btn btn-success" wire:click="approve">Approve</button>
                </div>
            </div>
        </div>
    </div>
@endif