<div wire:ignore.self class="modal fade" id="item-edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Item - ({{ $selected_item->name ?? '' }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" wire:model="description">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control" wire:model="image" placeholder="https://...">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" wire:model="qty" min="0">
                        @error('qty') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" wire:model="price" min="0" step="0.01">
                        </div>
                        @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" wire:model="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" wire:click="update">Save</button>
            </div>
        </div>
    </div>
</div>