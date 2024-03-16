<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RecipeController
 */
final class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        $recipes = Recipe::factory()->count(3)->create();

        $response = $this->get(route('recipe.show', ['recipe' => $recipes->random(1)->first()->slug]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Recipe/RecipeShow')
        );
    }
}
