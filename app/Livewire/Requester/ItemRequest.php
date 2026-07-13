<?php

namespace App\Livewire\Requester;

use App\Models\Item;
use App\Models\DraftItem;
use App\Models\ItemRequest as ItemRequestModel;
use App\Models\ItemRequestItem;
use Livewire\Component;

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
        $qty = max(1, (int) $this->qty);

        $draft = DraftItem::where('user_id', auth()->id())
            ->where('item_id', $this->selected_item_id)
            ->first();

        if ($draft) {
            $draft->update(['quantity' => $draft->quantity + $qty]);
        } else {
            DraftItem::create([
                'user_id' => auth()->id(),
                'item_id' => $this->selected_item_id,
                'quantity' => $qty,
            ]);
        }

        $this->closeModal();
    }

    public function removeFromCart($itemId)
    {
        DraftItem::where('user_id', auth()->id())
            ->where('item_id', $itemId)
            ->delete();
    }

    public function submitRequest()
    {
        $draftItems = DraftItem::where('user_id', auth()->id())->get();

        if ($draftItems->isEmpty()) {
            session()->flash('error', 'Your cart is empty.');
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
            ]);
        }

        DraftItem::where('user_id', auth()->id())->delete();
        session()->flash('message', 'Request submitted successfully!');
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