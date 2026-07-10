<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequestItem extends Model
{
    use HasFactory;

    protected $table = 'request_items';
    protected $fillable = ['request_id', 'item_id', 'quantity', 'remarks'];
    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
