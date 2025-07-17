<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Category/CategoryIndex')->with([
            'categories' => fn () => Category::orderBy('order_column')
                ->withCount('recipes')
                ->withTrashed()
                ->get()
                ->makeVisible('id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:40', 'unique:categories'],
        ]);

        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')->withBanner("Successfully created {$category->name}");
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->withBanner("Deleted {$name}", BannerTypeEnum::danger);
    }

    public function restore(Category $category)
    {
        $name = $category->name;
        $category->restore();

        return redirect()->route('admin.categories.index')
            ->withBanner("Restored {$name}");
    }
}
