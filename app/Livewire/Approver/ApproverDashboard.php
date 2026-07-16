<?php

namespace App\Livewire\Approver;

use App\Models\ApprovalLevel;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\ItemRequestApproval;
use App\Models\ItemRequestItem;
use Livewire\Component;

class ApproverDashboard extends Component
{

    public $showModal = false;
    public $selected_request_id = null;
    public $remarks = '';
    public $approvedQuantities = [];

    public function viewRequest($requestId)
    {
        $this->selected_request_id = $requestId;
        $this->remarks = '';

        $items = ItemRequestItem::where('request_id', $requestId)->get();
        $this->approvedQuantities = $items->pluck('approved_quantity', 'id')->toArray();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selected_request_id = null;
        $this->remarks = '';
        $this->approvedQuantities = [];
    }

    public function incrementApprovedQty($itemId)
    {
        $item = ItemRequestItem::find($itemId);
        if (!$item) return;

        $current = $this->approvedQuantities[$itemId] ?? 0;

        if ($current >= $item->approved_quantity) {
            $this->dispatch('notify', type: 'error', message: 'Cannot exceed the current approved quantity.');
            return;
        }

        $this->approvedQuantities[$itemId] = $current + 1;
    }

    public function decrementApprovedQty($itemId)
    {
        $current = $this->approvedQuantities[$itemId] ?? 0;

        if ($current <= 0) {
            return;
        }

        $this->approvedQuantities[$itemId] = $current - 1;
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

        $items = ItemRequestItem::where('request_id', $request->id)->get();

        foreach ($items as $item) {
            $entered = (int) ($this->approvedQuantities[$item->id] ?? $item->approved_quantity);
            if ($entered < 0 || $entered > $item->approved_quantity) {
                $this->dispatch('notify', type: 'error', message: "Invalid quantity for {$item->item->name}.");
                return;
            }
        }

        $itemsToRemove = [];

        foreach ($items as $item) {
            $entered = (int) ($this->approvedQuantities[$item->id] ?? $item->approved_quantity);
            if ($entered === 0) {
                $itemsToRemove[] = $item;
            } else {
                $item->update(['approved_quantity' => $entered]);
            }
        }

        foreach ($itemsToRemove as $item) {
            $item->delete();
        }

        $remainingCount = ItemRequestItem::where('request_id', $request->id)->count();

        if ($remainingCount === 0) {
            ItemRequestApproval::where('request_id', $request->id)
                ->where('approval_level_id', $myLevel->id)
                ->update([
                    'status' => 'Declined',
                    'remarks' => $this->remarks ?: 'All items were removed during review.',
                    'approved_at' => now(),
                ]);

            ItemRequestApproval::where('request_id', $request->id)
                ->where('sequence', '>', $myLevel->sequence)
                ->update(['status' => 'Cancelled']);

            $request->update(['status' => 'Declined']);
            $this->dispatch('notify', type: 'success', message: 'All items were removed — request declined.');
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
            $remainingItems = ItemRequestItem::where('request_id', $request->id)->get();

            foreach ($remainingItems as $item) {
                $stockItem = \App\Models\Item::find($item->item_id);
                if ($stockItem) {
                    $newQty = max(0, $stockItem->qty - $item->approved_quantity);
                    $stockItem->update(['qty' => $newQty]);
                }
            }

            $request->update(['status' => 'Completed']);
            $this->dispatch('notify', type: 'success', message: 'Request approved and fulfilled.');
        } else {
            $request->update(['current_sequence' => $myLevel->sequence + 1]);
            $this->dispatch('notify', type: 'success', message: 'Request approved.');
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
