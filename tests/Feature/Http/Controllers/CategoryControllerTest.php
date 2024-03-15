<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CategoryController
 */
final class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->get(route('category.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Category/CategoryIndex')
        );
    }

    #[Test]
    public function show_displays_view(): void
    {
        $categories = Category::factory()->count(3)->create();
        $randomCategory = $categories->random(1)->first();

        $response = $this->get(route('category.show', ['category' => $randomCategory->slug]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Category/CategoryShow')
            ->where('category.name', $randomCategory->name)
        );
    }
}
