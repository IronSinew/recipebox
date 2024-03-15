<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Category/CategoryIndex')->with([
            'categories' => fn () => Category::withCount('recipes')->get(),
        ]);
    }

    public function show(Category $category, Request $request)
    {
        return Inertia::render('Category/CategoryShow')->with([
            'category' => fn () => $category,
            'recipes' => fn () => $category->recipes,
        ]);
    }
}
