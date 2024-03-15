<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSimpleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(Recipe::search($request->search)->get('slug', 'name'));
    }
}
