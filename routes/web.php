<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect('/admin/items');
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

Route::middleware('auth')->group(function () {
    Route::get('/request/items', function () {
        return view('requester.item-request');
    })->name('requester.items');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
