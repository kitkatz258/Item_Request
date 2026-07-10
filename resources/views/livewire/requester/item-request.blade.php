<div>
    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
                @forelse($items as $item)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm" style="cursor:pointer;"
                            wire:click="openItem({{ $item->id }})">
                            <img src="{{ $item->image ?? 'https://placehold.co/300x200?text=' . urlencode($item->name) }}"
                                class="card-img-top" style="height:160px; object-fit:cover;">
                            <div class="card-body">
                                <h6 class="card-title mb-1">{{ $item->name }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($item->description, 50) }}</p>
                                <span class="badge bg-secondary">{{ $item->qty }} available</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted">No items available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Your Card</span>
                    <span class="badge bg-primary">{{ count($cart) }} items</span>
                </div>
                <div class="card-body">
                    @if(empty($cart))
                        <p class="text-muted text-center">No items in cart yet.</p>
                    @else
                        <table class="table table-sm">
                            <tbody>
                                @foreach($cart as $itemId => $details)
                                    <tr>
                                        <td>{{ $details['name'] }}</td>
                                        <td class="text-center">x{{ $details['quantity'] }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger"
                                                wire:click="removeFromCart({{ $itemId }})">
                                                &times;
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button class="btn btn-success w-100 mt-2" wire:click="submitRequest">
                            Submit Request
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('livewire.requester.modal.item-detail-modal')

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-item-detail', () => {
                new bootstrap.Modal(document.getElementById('item-detail')).show();
            });
        });
    </script>
</div>
