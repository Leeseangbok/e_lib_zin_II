<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LibraryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC-FACING ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


// --- AUTHENTICATED USER ACTIONS ---
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Library (Favorites)
    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::post('/library/add', [LibraryController::class, 'add'])->name('library.add');
    Route::delete('/library/remove', [LibraryController::class, 'remove'])->name('library.remove');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Reading
    Route::get('/books/{id}/read', [BookController::class, 'read'])->name('books.read');
});


// --- AUTHENTICATION & ADMIN ROUTES ---
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
