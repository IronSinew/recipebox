<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AllRecipeListController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPageAmount = 9;

        //@codeCoverageIgnoreStart
        if ($request->wantsJson()) {
            return Recipe::orderBy('id')->cursorPaginate($perPageAmount);
        }
        //@codeCoverageIgnoreEnd

        return Inertia::render('Recipe/RecipeList')->with([
            'label' => fn () => collect(['name' => 'All Recipes']),
            'recipes' => fn () => Recipe::orderBy('id')->cursorPaginate($perPageAmount),
        ]);
    }
}
