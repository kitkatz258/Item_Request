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