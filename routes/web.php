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
// This is the new personalized home page for logged-in users.
Route::get('/{user:name}/home', [ProfileController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('home');

// Book and Category Browse routes are public.
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


// --- ADMIN-ONLY ROUTE ---
// The dashboard is now for admins. We use a separate middleware for this.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); // We'll add an 'admin' middleware later


// --- AUTHENTICATED USER ACTIONS ---
Route::middleware('auth')->group(function () {
    // Profile editing routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Review and Library routes
    Route::post('/reviews/{book}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/my-library', [LibraryController::class, 'index'])->name('library.index');
    Route::post('/library/add/{book}', [LibraryController::class, 'add'])->name('library.add');
    Route::delete('/library/remove/{book}', [LibraryController::class, 'remove'])->name('library.remove');
    Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');
});

// --- DO NOT EDIT BELOW ---
// Includes all the necessary login, registration, password reset routes, etc.
require __DIR__ . '/auth.php';
