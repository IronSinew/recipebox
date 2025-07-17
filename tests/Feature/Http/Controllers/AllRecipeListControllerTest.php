<?php

use App\Models\Recipe;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('index displays view', function () {
    Recipe::factory()->count(5)->create();

    $response = $this->get(route('recipe.all'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Recipe/RecipeList')
        ->has('recipes.data', 5)
    );
});
