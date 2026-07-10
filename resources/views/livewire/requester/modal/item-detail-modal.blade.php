@if($showModal)
<div class="modal d-block" style="background: rgba(0, 0, 0, .5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <pre>@dump($selected_item)</pre> -->
                <h5 class="modal-title">{{ $selected_item->name ?? '' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($selected_item)
                    <img src="{{ $selected_item->image ?? 'https://placehold.co/400x200?text=' . urlencode($selected_item->name) }}"
                        class="img-fluid rounded mb-3" style="width:100%; height:200px; object-fit:cover;">
                    <p class="text-muted">{{ $selected_item->description }}</p>
                    <p><strong>Available:</strong> {{ $selected_item->qty }}</p>
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <label class="form-label mb-0">Quantity:</label>
                        <input type="number" class="form-control" style="width:100px;"
                            wire:model="quantities.{{ $selected_item->id }}" min="1" value="1">
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-close" wire:click="closeModal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif