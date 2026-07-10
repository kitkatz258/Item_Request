<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $table = 'requests'; 
    protected $fillable = ['user_id', 'status', 'current_sequence', 'remarks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItems()
    {
        return $this->hasMany(ItemRequestItem::class, 'request_id');
    }

    public function requestApprovals()
    {
        return $this->hasMany(ItemRequestApproval::class, 'request_id');
    }
}