<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use App\Services\GutendexService;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    public function index()
    {
        $bookCount = $this->gutendexService->getBooks()['count'] ?? 0;
        $latestBooks = $this->gutendexService->getBooks(1, null, 'new')['results'] ?? [];
        $userCount = User::count();
        $latestUsers = User::latest()->take(5)->get();
        $categoryCount = Category::count();
        $reviewCount = Review::count();

        return view('admin.dashboard', compact('bookCount', 'userCount', 'categoryCount', 'reviewCount', 'latestUsers', 'latestBooks'));
    }
}
