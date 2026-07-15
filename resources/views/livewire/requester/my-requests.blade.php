<div>
    <h2 class="mb-3">My Requests</h2>

    @if($requests->isEmpty())
        <p class="text-muted">You haven't submitted any requests yet.</p>
    @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr wire:key="request-{{ $request->id }}">
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->request_items_count }} item(s)</td>
                        <td>
                            <span class="badge bg-{{ $request->status === 'Approved' ? 'success' : ($request->status === 'Declined' ? 'danger' : ($request->status === 'Cancelled' ? 'secondary' : 'warning')) }}">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" wire:click="viewRequest({{ $request->id }})">
                                View
                            </button>
                            
                            @if($request->status === 'Pending' && $request->current_sequence === 1)
                                <button class="btn btn-sm btn-outline-primary" wire:click="cancelRequest({{ $request->id }})"
                                    onclick="return confirm('Cancel this request?')">
                                    Cancel
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @include('livewire.requester.modal.request-detail-modal')
</div>