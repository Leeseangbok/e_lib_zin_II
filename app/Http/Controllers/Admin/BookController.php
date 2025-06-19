<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GutendexService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $gutendexService;

    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $books = $this->gutendexService->getBooks($page, $search);

        return view('admin.books.index', compact('books'));
    }
}
