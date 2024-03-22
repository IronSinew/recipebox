<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Label;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;

class RecipeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Recipe/RecipeIndex')->with([
            'recipes' => fn () => Recipe::withTrashed()
                ->with('labels', 'categories')
                ->orderBy('id', 'desc')
                ->get()
                ->makeVisible('id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'max:140', 'unique:recipes,name'],
            'serving' => ['required', 'string'],
            'cook_time' => ['required', 'integer'],
            'prep_time' => ['required', 'integer'],
            'ingredients' => ['required', 'string', 'min:10'],
            'instructions' => ['required', 'string', 'min:10'],
            'description' => ['nullable', 'string'],
            'labelsSelected' => ['array'],
            'categoriesSelected' => ['array'],
        ]) + ['user_id' => $request->user()->id];

        unset($validated['categoriesSelected'], $validated['labelsSelected']);

        $recipe = Recipe::create($validated);

        $recipe->labels()->sync(
            Label::whereIn('slug', $request->get('labelsSelected'))->pluck('id')->toArray()
        );

        $recipe->categories()->sync(
            Category::whereIn('slug', $request->get('categoriesSelected'))->pluck('id')->toArray()
        );

        return redirect()->route('admin.recipes.index')->withBanner("Successfully created {$recipe->name}");
    }

    public function edit(Recipe $recipe)
    {
        return Inertia::render('Admin/Recipe/RecipeEdit')->with([
            'recipe' => fn () => $recipe->makeVisible('id')->loadMissing('labels', 'categories', 'media'),
            'labels' => fn () => Label::withTrashed()
                ->orderBy('order_column')
                ->get()
                ->makeVisible('id'),
            'categories' => fn () => Category::withTrashed()
                ->orderBy('order_column')
                ->get()
                ->makeVisible('id'),
            'images' => fn () => $recipe->loadMissing('media')->media,
        ]);
    }

    public function update(Recipe $recipe, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:140', Rule::unique('recipes')->ignore($recipe->id)],
            'serving' => ['required', 'string'],
            'cook_time' => ['required', 'integer'],
            'prep_time' => ['required', 'integer'],
            'ingredients' => ['required', 'string', 'min:10'],
            'instructions' => ['required', 'string', 'min:10'],
            'description' => ['nullable', 'string'],
            'labelsSelected' => ['array'],
            'categoriesSelected' => ['array'],
        ]);

        $recipe->labels()->sync(
            Label::whereIn('slug', $request->get('labelsSelected'))->pluck('id')->toArray()
        );
        $recipe->categories()->sync(
            Category::whereIn('slug', $request->get('categoriesSelected'))->pluck('id')->toArray()
        );

        unset($validated['categoriesSelected'], $validated['labelsSelected']);
        $recipe->update($validated);

        return redirect()->back()->withBanner("Successfully updated {$recipe->name}");
    }

    public function create()
    {
        return Inertia::render('Admin/Recipe/RecipeEdit')->with([
            'recipe' => fn () => new Recipe(),
            'labels' => fn () => Label::withTrashed()
                ->orderBy('order_column')
                ->get()
                ->makeVisible('id'),
            'categories' => fn () => Category::withTrashed()
                ->orderBy('order_column')
                ->get()
                ->makeVisible('id'),
        ]);
    }

    public function destroy(Recipe $recipe)
    {
        $name = $recipe->name;
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->withBanner("Deleted {$name}", BannerTypeEnum::danger);
    }

    public function restore(Recipe $recipe)
    {
        $name = $recipe->name;
        $recipe->restore();

        return redirect()->route('admin.recipes.index')
            ->withBanner("Restored {$name}");
    }

    public function imageStore(Recipe $recipe, Request $request)
    {
        $request->validate([
            'images' => ['array'],
            'images.*' => ['required', File::image()->max(10 * 1024)],
        ]);

        foreach ($request->images as $key => $image) {
            $recipe->addMediaFromRequest("images.{$key}")
                ->setName("{$recipe->name} Image")
                ->setFileName(sprintf('%s-%s.%s', $recipe->slug, \Str::random(6), $image->extension()))
                ->preservingOriginal()
                ->toMediaCollection();
        }

        return response()->noContent();
    }
}
