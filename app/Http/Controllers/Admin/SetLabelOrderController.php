<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;

/** @codeCoverageIgnore */
class SetLabelOrderController extends Controller
{
    public function __invoke(Request $request)
    {
        Label::setNewOrder($request->all());

        return response()->noContent();
    }
}
