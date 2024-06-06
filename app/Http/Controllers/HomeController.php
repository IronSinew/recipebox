<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard')->with([
            'most_recent_recipes' => Inertia::lazy(fn () => Recipe::with('media', 'labels', 'categories')
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get()),

            'categories' => fn () => Category::withCount('recipes')->get(),
        ]);
    }
}
