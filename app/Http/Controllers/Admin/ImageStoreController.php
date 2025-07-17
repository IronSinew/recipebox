<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class ImageStoreController extends Controller
{
    public function __invoke(Recipe $recipe, Request $request)
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
