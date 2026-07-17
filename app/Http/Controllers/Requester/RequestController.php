<?php

namespace App\Http\Controllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function fetch()
    {
        $requests = ItemRequest::where('user_id', auth()->id())
            ->withCount('requestItems')
            ->latest('id');

        return DataTables::of($requests)
            ->addColumn('items', fn($r) => $r->request_items_count . ' item(s)')
            ->addColumn('status_badge', function ($r) {
                $color = match ($r->status) {
                    'Approved', 'Completed' => 'success',
                    'Declined' => 'danger',
                    'Cancelled' => 'secondary',
                    default => 'warning',
                };
                return "<span class=\"badge bg-{$color}\">{$r->status}</span>";
            })
            ->addColumn('submitted', fn($r) => $r->created_at->format('M d, Y'))
            ->addColumn('action', function ($r) {
                $btn = '<button class="btn btn-sm btn-outline-primary" onclick="viewRequest(' . $r->id . ')">View</button>';
                if ($r->status === 'Pending' && $r->current_sequence === 1) {
                    $btn .= ' <button class="btn btn-sm btn-outline-danger" onclick="cancelRequestConfirm(' . $r->id . ')">Cancel</button>';
                }
                return $btn;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
