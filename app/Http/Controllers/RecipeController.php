<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    public function __invoke(Recipe $recipe, Request $request): Response
    {
        $recipe->loadMissing('labels', 'categories', 'media');

        return Inertia::render('Recipe/RecipeShow')->with([
            'recipe' => fn () => $recipe,
        ]);
    }
}
