<?php

use App\Models\Recipe;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('search gives results', function () {
    Recipe::factory()->create([
        'name' => 'Mac and Cheese',
    ]);

    Recipe::factory()->create([
        'name' => 'Fruit Salad',
    ]);

    $response = $this->postJson(route('search.simple'), ['search' => 'Mac']);

    $response->assertOk();

    $response->assertJsonPath('0.name', 'Mac and Cheese');
    expect(Recipe::count())->toBeGreaterThan(0);
});
