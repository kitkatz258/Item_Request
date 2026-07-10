<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
    use HasFactory;

    protected $table = 'approval_levels';
    protected $fillable = ['user_id', 'sequence', 'label', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}