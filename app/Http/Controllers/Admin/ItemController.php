<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function fetch()
    {
    $data = Item::query();

    return DataTables::eloquent($data)
        ->addColumn('status', function ($row) {
            return $row->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';
        })
        ->addColumn('action', function ($row) {
            return '<button class="btn btn-sm btn-warning"
                onclick="Livewire.dispatch(\'editItem\', { id: ' . $row->id . ' })">
                Edit
            </button>';
        })
        ->rawColumns(['status', 'action'])
        ->make(true);
    }
}
