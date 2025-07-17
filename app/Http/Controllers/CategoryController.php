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
        $perPageAmount = 9;

        // @codeCoverageIgnoreStart
        if ($request->wantsJson()) {
            return $category->recipes()->orderBy('id')->cursorPaginate($perPageAmount);
        }
        // @codeCoverageIgnoreEnd

        return Inertia::render('Recipe/RecipeList')->with([
            'label' => fn () => $category,
            'recipes' => fn () => $category->recipes()->orderBy('id')->cursorPaginate($perPageAmount),
        ]);
    }
}
