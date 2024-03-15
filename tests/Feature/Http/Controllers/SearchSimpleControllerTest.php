<?php

namespace Http\Controllers;

use App\Models\Recipe;
use Database\Seeders\BasicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RecipeController
 */
final class SearchSimpleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function search_gives_results(): void
    {
        Recipe::factory()->create([
            "name" => "Mac and Cheese",
        ]);

        Recipe::factory()->create([
            "name" => "Fruit Salad",
        ]);

        $response = $this->postJson(route('search.simple'), ["search" => "Mac"]);

        $response->assertOk();

        $response->assertJsonPath("0.name",  "Mac and Cheese");
        $this->assertGreaterThan(0, Recipe::count());
    }
}
