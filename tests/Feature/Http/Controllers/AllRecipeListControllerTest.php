<?php

namespace Http\Controllers;

use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RecipeController
 */
final class AllRecipeListControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        Recipe::factory()->count(5)->create();

        $response = $this->get(route('recipe.all'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Recipe/RecipeList')
            ->has('recipes.data', 5)
        );
    }
}
