<?php

namespace Http\Controllers;

use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CategoryController
 */
final class LabelControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_displays_view(): void
    {
        Label::factory()->count(3)->create();

        $response = $this->get(route('label.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component("Label/LabelIndex")
            ->has("labels", 3)
        );
    }

    #[Test]
    public function show_displays_view(): void
    {
        $labels = Label::factory()->count(3)->create();
        $randomLabel = $labels->random(1)->first();

        $response = $this->get(route('label.show', ["label" => $randomLabel->slug]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component("Label/LabelShow")
            ->where("label.name", $randomLabel->name)
        );
    }
}
