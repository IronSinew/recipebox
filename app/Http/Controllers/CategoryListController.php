<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryListController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(Category::select('slug', 'name')->get());
    }
}
