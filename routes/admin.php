<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Book Management
    // This single line provides all the necessary routes for a resource:
    // index, create, store, show, edit, update, destroy
    Route::resource('books', BookController::class);

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});
