<?php

use App\Models\Category;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('index displays view', function () {
    Category::factory()->count(3)->create();

    $response = $this->get(route('category.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Category/CategoryIndex')
    );
});

test('show displays view', function () {
    $categories = Category::factory()->count(3)->create();
    $randomCategory = $categories->random(1)->first();

    $response = $this->get(route('category.show', ['category' => $randomCategory->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Recipe/RecipeList')
        ->where('label.name', $randomCategory->name)
    );
});
