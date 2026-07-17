<?php

namespace App\Http\Controllers\Approver;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLevel;
use App\Models\ItemRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ApproverController extends Controller
{
    public function fetch()
    {
        $myLevel = ApprovalLevel::where('user_id', auth()->id())->first();

        $requests = $myLevel
            ? ItemRequest::where('status', 'Pending')
                ->where('current_sequence', $myLevel->sequence)
                ->withCount('requestItems')
                ->with('user')
                ->latest('id')
            : ItemRequest::whereRaw('1 = 0');

        return DataTables::of($requests)
            ->addColumn('requested_by', fn($r) => $r->user->name)
            ->addColumn('items', fn($r) => $r->request_items_count . ' item(s)')
            ->addColumn('submitted', fn($r) => $r->created_at->format('M d, Y'))
            ->addColumn('action', function ($r) {
                return '<button class="btn btn-sm btn-outline-primary" onclick="reviewRequest(' . $r->id . ')">Review</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
