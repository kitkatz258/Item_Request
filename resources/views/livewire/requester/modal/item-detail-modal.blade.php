@if($showModal && $selectedItem)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $selectedItem->name }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ $selectedItem->description }}</p>
                    <p class="text-muted">{{ $selectedItem->qty }} available</p>
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" wire:model="qty" min="1" max="{{ $selectedItem->qty }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="addToCart">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
@endif