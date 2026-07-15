<div>
    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h2 class="mb-1">Approver Dashboard</h2>
    @if($myLevel)
        <p class="text-muted">Step {{ $myLevel->sequence }}: {{ $myLevel->label }}</p>
    @else
        <p class="text-danger">No approval level assigned to your account.</p>
    @endif

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
</div>