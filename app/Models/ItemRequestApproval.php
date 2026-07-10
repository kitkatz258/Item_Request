<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequestApproval extends Model
{
    use HasFactory;

    protected $table = 'request_approvals';
    protected $fillable = ['request_id', 'approval_level_id', 'sequence', 'status', 'remarks', 'approved_at'];

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    public function approvalLevel()
    {
        return $this->belongsTo(ApprovalLevel::class, 'approval_level_id');
    }
}
