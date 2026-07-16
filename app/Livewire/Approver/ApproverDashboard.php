<?php

namespace App\Livewire\Approver;

use App\Models\ApprovalLevel;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\ItemRequestApproval;
use Livewire\Component;

class ApproverDashboard extends Component
{

    public $showModal = false;
    public $selected_request_id = null;
    public $remarks = '';

    public function viewRequest($requestId)
    {
        $this->selected_request_id = $requestId;
        $this->remarks = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selected_request_id = null;
        $this->remarks = '';
    }

    public function approve()
    {
        $myLevel = ApprovalLevel::where('user_id', auth()->id())->first();
        $request = ItemRequest::find($this->selected_request_id);

        if (!$myLevel || !$request || $request->current_sequence !== $myLevel->sequence) {
            $this->dispatch('notify', type: 'error', message: 'This request is not awaiting your approval.');
            $this->closeModal();
            return;
        }
    

        $approval = ItemRequestApproval::where('request_id', $request->id)
            ->where('approval_level_id', $myLevel->id)
            ->first();

        $approval->update([
            'status' => 'Approved',
            'remarks' => $this->remarks,
            'approved_at' => now(),
        ]);

        $isLastStep = !ApprovalLevel::where('sequence', '>', $myLevel->sequence)->exists();

        if ($isLastStep) {
            foreach ($request->requestItems as $ri) {
                $item = Item::find($ri->item_id);
                if ($item) {
                    $newQty = max(0, $item->qty - $ri->quantity);
                    $item->update(['qty' => $newQty]);
                }
            }

            $request->update(['status' => 'Completed']);
            $this->dispatch('notify', type: 'success', message: 'Request approved and fulfilled.');
        } else {
            $request->update(['current_sequence' => $myLevel->sequence + 1]);
            $this->dispatch('notify', type: 'success', message: 'Request approved');
        }

        $this->closeModal();
    }

    public function decline()
    {
        $this->validate([
            'remarks' => 'required|string|min:3',
        ], [
            'remarks.required' => 'Please provide a reason for declining.',
        ]);

        $myLevel = ApprovalLevel::where('user_id', auth()->id())->first();
        $request = ItemRequest::find($this->selected_request_id);

        if (!$myLevel || !$request || $request->current_sequence !== $myLevel->sequence) {
            $this->dispatch('notify', type: 'error', message: 'This request is not awaiting your approval.');
            $this->closeModal();
            return;
        }

        ItemRequestApproval::where('request_id', $request->id)
            ->where('approval_level_id', $myLevel->id)
            ->update([
                'status' => 'Declined',
                'remarks' => $this->remarks,
                'approved_at' => now(),
            ]);

        ItemRequestApproval::where('request_id', $request->id)
            ->where('sequence', '>', $myLevel->sequence)
            ->update(['status' => 'Cancelled']);

        $request->update(['status' => 'Declined']);

        $this->dispatch('notify', type: 'success', message: 'Request declined.');
        $this->closeModal();    
    }

    public function render()
    {
        $myLevel = ApprovalLevel::where('user_id', auth()->id())->first();

        $requests = $myLevel
            ? ItemRequest::where('status', 'Pending')
                ->where('current_sequence', $myLevel->sequence)
                ->withCount('requestItems')
                ->with('user')
                ->latest()
                ->get()
            : collect();

        return view('livewire.approver.approver-dashboard', [
            'requests' => $requests,
            'myLevel' => $myLevel,
            'selectedRequest' => $this->selected_request_id
                ? ItemRequest::with([
                    'requestItems.item', 
                    'user',
                    'requestApprovals' => fn ($q) => $q->orderBy('sequence')
                    ])->find($this->selected_request_id)
                : null,
        ]);
    }
}
