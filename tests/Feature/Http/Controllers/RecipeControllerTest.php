<?php

use App\Models\Recipe;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('index displays view', function () {
    $recipes = Recipe::factory()->count(3)->create();

    $response = $this->get(route('recipe.show', ['recipe' => $recipes->random(1)->first()->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Recipe/RecipeShow')
    );
});
