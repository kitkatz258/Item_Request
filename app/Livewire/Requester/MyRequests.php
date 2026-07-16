<?php

namespace App\Livewire\Requester;

use App\Models\ItemRequest;
use App\Models\ItemRequestApproval;
use Livewire\Component;

class MyRequests extends Component
{
    public $showModal = false;
    public $selected_request_id = null;

    public function viewRequest($requestId)
    {
        $this->selected_request_id = $requestId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selected_request_id = null;
    }

    public function cancelRequest($requestId)
    {
        $request = ItemRequest::where('id', $requestId)
            ->where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->where('current_sequence', 1)
            ->first();

        if(!$request) {
            $this->dispatch('notify', type: 'error', message: 'This request can no longer be cancelled.');
            return;
        }

        $request->update(['status' => 'Cancelled']);

        \App\Models\ItemRequestApproval::where('request_id', $request->id)
            ->update(['status' => 'Cancelled']);
        
        $this->dispatch('notify', type: 'success', message: 'Request cancelled.');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.requester.my-requests', [
            'requests' => ItemRequest::where('user_id', auth()->id())
                ->withCount('requestItems')
                ->latest()
                ->get(),
            'selectedRequest' => $this->selected_request_id
                ? ItemRequest::with([
                    'requestItems.item',
                    'requestApprovals' => fn ($q) => $q->orderBy('sequence'),
                    'requestApprovals.approvalLevel',
                ])->find($this->selected_request_id)
                : null,
        ]);
    }
}
