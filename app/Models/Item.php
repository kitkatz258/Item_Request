<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $fillable = ['name', 'description', 'image', 'qty', 'status'];

    public function itemRequestItems()
    {
        return $this->hasMany(ItemRequestItem::class, 'item_id');
    }
}