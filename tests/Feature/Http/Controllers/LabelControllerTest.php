<?php

use App\Models\Label;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('index displays view', function () {
    Label::factory()->count(3)->create();

    $response = $this->get(route('label.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Label/LabelIndex')
        ->has('labels', 3)
    );
});

test('show displays view', function () {
    $labels = Label::factory()->count(3)->create();
    $randomLabel = $labels->random(1)->first();

    $response = $this->get(route('label.show', ['label' => $randomLabel->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Recipe/RecipeList')
        ->where('label.name', $randomLabel->name)
    );
});
