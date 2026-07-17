<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Approver\ApproverController;
use App\Http\Controllers\Requester\RequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect('/admin/items');
    }
    if (auth()->user()->role === 'approver') {
        return redirect('/approver/requests');
    }
    return redirect('/request/items');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/items', function () {
        return view('admin.items');
    })->name('admin.items');
    Route::get('/admin/items/fetch', [ItemController::class, 'fetch'])
        ->name('admin.items.fetch');
});

Route::middleware(['auth', 'approver'])->group(function () {
    Route::get('/approver/requests', function () {
        return view('approver.requests');
    })->name('approver.requests');

    Route::get('/approver/requests/fetch', [ApproverController::class, 'fetch'])
        ->name('approver.requests.fetch');
});

Route::middleware('auth')->group(function () {
    Route::get('/request/items', function () {
        return view('requester.item-request');
    })->name('requester.items');

    Route::get('/my-requests', function () {
        return view('requester.my-requests');
    })->name('requester.my-requests');

    Route::get('/my-requests/fetch', [RequestController::class, 'fetch'])
        ->name('requester.my-requests.fetch');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
