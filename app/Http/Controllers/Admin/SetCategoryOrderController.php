<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/** @codeCoverageIgnore */
class SetCategoryOrderController extends Controller
{
    public function __invoke(Request $request)
    {
        Category::setNewOrder($request->all());

        return response()->noContent();
    }
}
