<?php

namespace App\Livewire\Requester;

use App\Models\ApprovalLevel;
use App\Models\Item;
use App\Models\DraftItem;
use App\Models\ItemRequest as ItemRequestModel;
use App\Models\ItemRequestApproval;
use App\Models\ItemRequestItem;
use Livewire\Component;

use function PHPSTORM_META\type;

class ItemRequest extends Component
{
    public $items;
    public $showModal = false;
    public $selected_item_id = null;
    public $qty = 1;

    public function mount()
    {
        $this->items = Item::where('status', true)->get();
    }

    public function openItem($itemId)
    {
        $this->selected_item_id = $itemId;
        $this->qty = 1;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selected_item_id = null;
        $this->qty = 1;
    }

    public function addToCart()
    {
        $item = Item::find($this->selected_item_id);

        if(!$item) {
            $this->dispatch('notify', type: 'error', message: 'Item not Found.');
            return;
        }
        
        $qty = (int) $this->qty;

        if($qty <= 0) {
            $this->dispatch('notify', type:'error', message: 'Quantity must be atleast 1');
            return;
        }

        if($qty > $item->qty) {
            $this->dispatch('notify', type: 'error', message: "Only {$item->qty} {$item->name} available");
            return;
        }

        $draft = DraftItem::where('user_id', auth()->id())
            ->where('item_id', $this->selected_item_id)
            ->first();

        if ($draft) {
            $newTotal = $draft->quantity + $qty;
            if($newTotal > $item->qty) {
                $this->dispatch('notify', type: 'error', message: "Cannot add more than the available stock ({$item->qty}).");
                return;
            }

            $draft->update(['quantity' => $draft->quantity + $qty]);
        } else {
            DraftItem::create([
                'user_id' => auth()->id(),
                'item_id' => $this->selected_item_id,
                'quantity' => $qty,
            ]);
        }

        $this->dispatch('notify', type: 'success', message: 'Added to cart.');
        $this->closeModal();
    }

    public function incrementCartItem($itemId)
    {
        $item = Item::find($itemId);
        $draft = DraftItem::where('user_id', auth()->id())
            ->where('item_id', $itemId)
            ->first();

        if(!$draft || !$item) {
            return;
        }

        if($draft->quantity >= $item->qty) {
            $this->dispatch('notify', type: 'error', message: "Only {$item->qty} {$item->name} available.");
            return;
        }

        $draft->increment('quantity');
    }

    public function decrementCartItem($itemId)
    {
        $draft = DraftItem::where('user_id', auth()->id())
            ->where('item_id', $itemId)
            ->first();

        if(!$draft) {
            return;
        }

        if($draft->quantity <= 1) {
            $draft->delete();
        } else {
            $draft->decrement('quantity');
        }
    }

    public function submitRequest()
    {
        $draftItems = DraftItem::where('user_id', auth()->id())->get();

        if($draftItems->isEmpty()) {
            $this->dispatch('notify', type: 'error', message: 'Your request is empty');
            return;
        }

        $request = ItemRequestModel::create([
            'user_id' => auth()->id(),
            'status' => 'Pending',
            'current_sequence' => 1,
        ]);

        foreach ($draftItems as $draft) {
            ItemRequestItem::create([
                'request_id' => $request->id,
                'item_id' => $draft->item_id,
                'quantity' => $draft->quantity,
                'approved_quantity' => $draft->quantity,
            ]);
        }

        $levels = ApprovalLevel::orderBy('sequence')->get();

        foreach ($levels as $level) {
            ItemRequestApproval::create([
                'request_id' => $request->id,
                'approval_level_id' => $level->id,
                'sequence' => $level->sequence,
                'status' => 'Pending',
            ]);
        }

        DraftItem::where('user_id', auth()->id())->delete();
        $this->dispatch('notify', type: 'success', message: 'Request submitted successfully!');
    }

    public function render()
    {
        return view('livewire.requester.item-request', [
            'items' => Item::where('status', true)->get(),
            'draftItems' => DraftItem::with('item')
                ->where('user_id', auth()->id())
                ->get(),
            'selectedItem' => $this->selected_item_id
                ? Item::find($this->selected_item_id)
                : null,
        ]);
    }
}