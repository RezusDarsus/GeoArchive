<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['categories' => Category::withCount('artifacts')->orderBy('name')->get()]);
    }

    public function show(Category $category): View
    {
        return view('categories.show', [
            'category' => $category,
            'artifacts' => $category->artifacts()->with(['category', 'tags'])->latest()->paginate(9),
        ]);
    }
}
