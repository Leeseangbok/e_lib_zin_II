<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LibraryController;

// Route for the public-facing welcome page with carousels.
Route::get('/', [HomeController::class, 'index'])->name('welcome');

// --- USER-FACING ROUTES ---
// Route::get('/dashboard', [ProfileController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

// Book and Category Browse routes are public.
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


// --- AUTHENTICATED USER ACTIONS ---
Route::middleware('auth')->group(function () {
    // Profile editing routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Review and Library routes
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/library/add/{book}', [LibraryController::class, 'add'])->name('library.add');
    Route::delete('/library/remove/{book}', [LibraryController::class, 'remove'])->name('library.remove');
    Route::get('/my-library', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/books/{id}/read', [BookController::class, 'read'])->name('books.read');
});

// Includes all the necessary login, registration, password reset routes, etc.
require __DIR__ . '/auth.php';

// Admin routes
require __DIR__ . '/admin.php';
