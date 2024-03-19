<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Label/LabelIndex')->with([
            'labels' => fn () => Label::orderBy('order_column')->withCount('recipes')
                ->withTrashed()
                ->get()
                ->makeVisible('id'),
        ]);
    }

    /** @codeCoverageIgnore  */
    public function setNewOrder(Request $request)
    {
        Label::setNewOrder($request->all());

        return response()->noContent();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:40', 'unique:labels'],
        ]);

        $label = Label::create($validated);

        return redirect()->route('admin.labels.index')->withBanner("Successfully created {$label->name}");
    }

    public function destroy(Label $label)
    {
        $name = $label->name;
        $label->delete();

        return redirect()->route('admin.labels.index')
            ->withBanner("Deleted {$name}", BannerTypeEnum::danger);
    }

    public function restore(Label $label)
    {
        $name = $label->name;
        $label->restore();

        return redirect()->route('admin.labels.index')
            ->withBanner("Restored {$name}");
    }
}
