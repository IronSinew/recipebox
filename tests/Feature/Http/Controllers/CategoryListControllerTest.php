<?php

use App\Models\Category;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('index displays view', function () {
    $categories = Category::factory()->count(5)->create();

    $response = $this->get(route('category.list'));

    $response->assertOk();
    $response->assertJsonCount(5);
});
