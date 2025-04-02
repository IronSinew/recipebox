<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchResource;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSimpleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            SearchResource::collection(Recipe::search($request->search)->get())
        );
    }
}
