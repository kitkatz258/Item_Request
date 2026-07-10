<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="mb-3">
        <button class="btn btn-primary" 
            onclick="Livewire.dispatch('showAddModal')">
            Add Item
        </button>
    </div>

    <div wire:ignore>
        <table class="table table-bordered w-100" id="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @include('livewire.admin.modal.item-add-modal')
    @include('livewire.admin.modal.item-edit-modal')

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-modal-item-edit', () => {
                new bootstrap.Modal(document.getElementById('item-edit')).show();
            });

            Livewire.on('showAddModal', () => {
                new bootstrap.Modal(document.getElementById('item-add')).show();
            });

            Livewire.on('closeModals', () => {
                bootstrap.Modal.getInstance(document.getElementById('item-edit'))?.hide();
                bootstrap.Modal.getInstance(document.getElementById('item-add'))?.hide();
                $('#items-table').DataTable().ajax.reload();
            });

            Livewire.on('editItem', (data) => {
                @this.call('edit', data.id);
            });
        });
    </script>
</div>