<?php

namespace App\Livewire\Requester;

use App\Models\Item;
use Livewire\Component;

class ItemRequest extends Component
{
    public $cart = [];
    public $items;
    public $quantities = [];
    public ?Item $selected_item = null;
    public bool $showModal = false;

    public function mount()
    {
        $this->items = Item::where('status', true)->get();
    }

    public function addToCart($itemId)
    {
        $item = $this->selected_item;
        $qty = $this->quantities[$item->id];

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity'] += $qty;
        } else {
            $this->cart[$itemId] = [
                'name' => $item->name,
                'quantity' => $qty,
            ];
        }

        $this->quantities[$itemId] = 1;
        $this->closeModal();
    }

    public function removeFromCart($itemId)
    {
        unset($this->cart[$itemId]);
    }

    public function openItem($id)
    {
        $this->selected_item = Item::findOrFail($id);
        // dd($this->selected_item);
        $this->showModal = true;

        if(!isset($this->quantities[$id])) {
            $this->quantities[$id] = 1;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selected_item = null;
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
