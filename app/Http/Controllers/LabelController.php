<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        return Inertia::render('Label/LabelShow')->with([
            'label' => fn () => $label,
            'recipes' => fn () => $label->recipes,
        ]);
    }
}
