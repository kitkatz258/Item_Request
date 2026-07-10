<?php

namespace App\Livewire\Requester;

use App\Models\Item;
use Livewire\Component;

class ItemRequest extends Component
{
    public $cart = [];
    public $items;
    public $quantities = [];
    public $selected_item;

    public function mount()
    {
        $this->items = Item::where('status', true)->get();
    }

    public function addToCart($itemId)
    {
        $item = Item::findOrFail($itemId);
        $qty = $this->quantities[$itemId] ?? 1;

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity'] += $qty;
        } else {
            $this->cart[$itemId] = [
                'name' => $item->name,
                'quantity' => $qty,
            ];
        }

        $this->quantities[$itemId] = 1;
    }

    public function removeFromCart($itemId)
    {
        unset($this->cart[$itemId]);
    }

    public function openItem($itemId)
    {
        $this->selected_item = Item::findOrFail($itemId);
        // dd($this->selected_item);
        $this->dispatch('show-item-detail');
    }

    public function submitRequest()
    {
        if(empty($this->cart)){
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $request = \App\Models\ItemRequest::create([
            'user_id' => auth()->id(),
            'status' => 'Pending',
            'current_sequence' => 1,
        ]);

        foreach($this->cart as $itemId => $details) {
            \App\Models\ItemRequestItem::create([
                'request_id' => $request->id,
                'item_id' => $itemId,
                'quantity' => $details['quantity'],
            ]);
        }

        $this->cart = [];
        session()->flash('message', 'Request submitted successfully!');
    }

    public function render()
    {
        return view('livewire.requester.item-request');
    }
}
