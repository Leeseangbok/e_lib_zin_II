<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController; // Add this
use App\Http\Controllers\Admin\UserController; // Add this

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Add this resource route for book management
Route::resource('books', BookController::class);

Route::get('users', [UserController::class, 'index'])->name('users.index');
