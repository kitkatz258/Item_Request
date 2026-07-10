<?php

namespace App\Livewire\Admin;

use App\Models\Item;
use Livewire\Component;

class ItemManagement extends Component
{
    public $items;
    public $name;
    public $description;
    public $qty;
    public $status = true;
    public $item_id;
    public $isEditing = false;
    public $selected_item;
    public $image;

    public function mount()
    {
        $this->items = Item::all();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'qty' => 'required|integer|min:0',
        ]);

        Item::create([
            'name' => $this->name,
            'description' => $this->description,
            'qty' => $this->qty,
            'status' => true,
            'images' => $this->image
        ]);

        
        $this->dispatch('closeModals');
        $this->reset(['name', 'description', 'qty']);
    }

    public function edit($id)
    {
        $this->selected_item = Item::findOrFail($id);
        $this->item_id = $this->selected_item->id;
        $this->name = $this->selected_item->name;
        $this->description = $this->selected_item->description;
        $this->qty = $this->selected_item->qty;
        $this->status = $this->selected_item->status;
        $this->image = $this->selected_item->image;
        $this->isEditing = true;
        $this->dispatch('show-modal-item-edit');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'qty' => 'required|integer|min:0',
        ]);

        Item::findOrFail($this->item_id)->update([
            'name' => $this->name,
            'description' => $this->description,
            'qty' => $this->qty,
            'status' => $this->status,
            'images' => $this->image
        ]);

        $this->reset(['name', 'description', 'qty', 'status', 'item_id', 'isEditing', 'selected_item']);
        $this->dispatch('closeModals');
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'description', 'qty', 'status', 'item_id', 'isEditing']);
    }

    public function toggleStatus($id)
    {
        $item = Item::findOrFail($id);
        $item->update(['status' => !$item->status]);
        $this->items = Item::all();
    }

    public function render()
    {
        return view('livewire.admin.item-management');
    }
}
