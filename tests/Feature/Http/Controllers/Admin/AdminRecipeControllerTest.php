<?php

namespace Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminRecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $nonAdminUser;

    protected User $contributorUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $this->contributorUser = User::factory()->create(['role' => UserRoleEnum::CONTRIBUTOR]);
        $this->nonAdminUser = User::factory()->create(['role' => null]);
    }

    #[Test]
    public function index_displays_view_to_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.recipes.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );
    }

    #[Test]
    public function index_displays_view_to_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $this->actingAs($this->contributorUser);
        $response = $this->get(route('admin.recipes.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );
    }

    #[Test]
    public function store_submit_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->post(route('admin.recipes.store'), [
            'name' => 'Foo',
            'serving' => '2 Foos',
            'ingredients' => '## Foo header',
            'instructions' => 'Mix all of the Foos',
            'description' => 'I am describing all of the Foos',
            'cook_time' => 15,
            'prep_time' => 10,
            'labelsSelected' => [],
            'categoriesSelected' => [],
        ]);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 4)
        );
    }

    #[Test]
    public function store_submit_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $this->actingAs($this->contributorUser);
        $response = $this->followingRedirects()->post(route('admin.recipes.store'), [
            'name' => 'Foo',
            'serving' => '2 Foos',
            'ingredients' => '## Foo header',
            'instructions' => 'Mix all of the Foos',
            'description' => 'I am describing all of the Foos',
            'cook_time' => 15,
            'prep_time' => 10,
            'labelsSelected' => [],
            'categoriesSelected' => [],
        ]);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 4)
        );
    }

    #[Test]
    public function update_submit_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->adminUser);

        $response = $this->followingRedirects()->put(route('admin.recipes.update', ['recipe' => $recipe]), [
            'name' => 'Foo',
            'serving' => '3 Foos',
            'ingredients' => '## Foo header',
            'instructions' => 'Mix all of the Foos',
            'description' => 'I am describing all of the Foos',
            'cook_time' => 15,
            'prep_time' => 10,
            'labelsSelected' => [],
            'categoriesSelected' => [],
        ]);

        $response->assertOk();
        $recipe->refresh();

        $this->assertSame('Foo', $recipe->name);
    }

    #[Test]
    public function update_submit_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->contributorUser);

        $response = $this->followingRedirects()->put(route('admin.recipes.update', ['recipe' => $recipe]), [
            'name' => 'Foo',
            'serving' => '3 Foos',
            'ingredients' => '## Foo header',
            'instructions' => 'Mix all of the Foos',
            'description' => 'I am describing all of the Foos',
            'cook_time' => 15,
            'prep_time' => 10,
            'labelsSelected' => [],
            'categoriesSelected' => [],
        ]);

        $response->assertOk();
        $recipe->refresh();

        $this->assertSame('Foo', $recipe->name);
    }

    #[Test]
    public function edit_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.recipes.edit', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeEdit')
            ->where('recipe.name', $recipe->name)
        );
    }

    #[Test]
    public function edit_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->contributorUser);
        $response = $this->get(route('admin.recipes.edit', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeEdit')
            ->where('recipe.name', $recipe->name)
        );
    }

    #[Test]
    public function create_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.recipes.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeEdit')
            ->missing('recipe.name')
            ->missing('recipe.id')
        );
    }

    #[Test]
    public function create_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->contributorUser);
        $response = $this->get(route('admin.recipes.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeEdit')
            ->missing('recipe.name')
            ->missing('recipe.id')
        );
    }

    #[Test]
    public function destroy_submit_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->delete(route('admin.recipes.destroy', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );

        $this->assertSame(1, Recipe::onlyTrashed()->count());
    }

    #[Test]
    public function destroy_submit_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();

        $this->actingAs($this->contributorUser);
        $response = $this->followingRedirects()->delete(route('admin.recipes.destroy', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );

        $this->assertSame(1, Recipe::onlyTrashed()->count());
    }

    #[Test]
    public function restore_submit_works_as_admin(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();
        $recipe->delete();

        $this->assertSame(1, Recipe::onlyTrashed()->count());

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->put(route('admin.recipes.restore', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );

        $this->assertSame(0, Recipe::onlyTrashed()->count());
    }

    #[Test]
    public function restore_submit_works_as_contributor(): void
    {
        Recipe::factory()->count(3)->create();
        $recipe = Recipe::first();
        $recipe->delete();

        $this->assertSame(1, Recipe::onlyTrashed()->count());

        $this->actingAs($this->contributorUser);
        $response = $this->followingRedirects()->put(route('admin.recipes.restore', ['recipe' => $recipe]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Recipe/RecipeIndex')
            ->has('recipes', 3)
        );

        $this->assertSame(0, Recipe::onlyTrashed()->count());
    }

    #[Test]
    public function index_does_not_display_view_to_nonadmin(): void
    {
        Recipe::factory()->count(3)->create();
        $this->actingAs($this->nonAdminUser);
        $response = $this->followingRedirects()->get(route('admin.recipes.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
    }
}
