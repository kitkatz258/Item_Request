<div>
    @if($requests->isEmpty())
        <p class="text-muted">No requests currently awaiting your approval.</p>
    @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Requested By</th>
                    <th>Items</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr wire:key="req-{{ $request->id }}">
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->request_items_count }} item(s)</td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" wire:click="viewRequest({{ $request->id }})">
                                Review
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('livewire.approver.modal.request-review-modal')

    <script>
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
    </script>
</div>