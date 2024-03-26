<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Label/LabelIndex')->with([
            'labels' => fn () => Label::withCount('recipes')->get(),
        ]);
    }

    public function show(Label $label, Request $request)
    {
        $perPageAmount = 9;

        if ($request->wantsJson()) {
            return $label->recipes()->orderBy('id')->cursorPaginate($perPageAmount);
        }

        return Inertia::render('Recipe/RecipeList')->with([
            'label' => fn () => $label,
            'recipes' => fn () => $label->recipes()->orderBy('id')->cursorPaginate($perPageAmount),
        ]);
    }
}
