<div>
    <h2 class="mb-1">Approver Dashboard</h2>
    @if($myLevel)
        <p class="text-muted">Step {{ $myLevel->sequence }}: {{ $myLevel->label }}</p>
    @else
        <p class="text-danger">No approval level assigned to your account. Contact the admin.</p>
    @endif

    <div wire:ignore>
        <table class="table table-hover w-100" id="approver-requests-table">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Requested By</th>
                    <th>Items</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @include('livewire.approver.modal.request-review-modal')

    <script>
        function initApproverTable() {
            if ($.fn.DataTable.isDataTable('#approver-requests-table')) {
                $('#approver-requests-table').DataTable().destroy();
            }

            $('#approver-requests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("approver.requests.fetch") }}',
                columns: [
                    { data: 'id', name: 'id', render: (data) => '#' + data },
                    { data: 'requested_by', name: 'user.name' },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'submitted', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });
        }

        document.addEventListener('livewire:navigated', initApproverTable);
        document.addEventListener('DOMContentLoaded', initApproverTable);

        function reviewRequest(id) {
            Livewire.dispatch('reviewRequest', { requestId: id });
        }

        function confirmDecrement(itemId, currentQty, itemName, totalItemsInRequest) {
            if (currentQty > 1) {
                Livewire.dispatch('decrementApprovedQty', { itemId: itemId });
                return;
            }

            const message = totalItemsInRequest === 1
                ? `This will remove ${itemName} and decline the entire request since it's the only item. Continue?`
                : `This will remove ${itemName} from the request. Continue?`;

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('decrementApprovedQty', { itemId: itemId });
                }
            });
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reloadTable', () => {
                $('#approver-requests-table').DataTable().ajax.reload();
            });
        });
    </script>
</div>