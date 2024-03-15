<?php

namespace Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CategoryController
 */
final class CategoryListControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        $categories = Category::factory()->count(5)->create();

        $response = $this->get(route('category.list'));

        $response->assertOk();
        $response->assertJsonCount(5);
    }
}
