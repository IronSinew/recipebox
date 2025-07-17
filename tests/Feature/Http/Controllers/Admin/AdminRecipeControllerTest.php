<?php

use App\Enums\UserRoleEnum;
use App\Models\Recipe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
    $this->contributorUser = User::factory()->create(['role' => UserRoleEnum::CONTRIBUTOR]);
    $this->nonAdminUser = User::factory()->create(['role' => null]);
});

test('index displays view to admin', function () {
    Recipe::factory()->count(3)->create();
    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.recipes.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );
});

test('index displays view to contributor', function () {
    Recipe::factory()->count(3)->create();
    $this->actingAs($this->contributorUser);
    $response = $this->get(route('admin.recipes.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );
});

test('store submit works as admin', function () {
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
});

test('store submit works as contributor', function () {
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
});

test('update submit works as admin', function () {
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

    expect($recipe->name)->toBe('Foo');
});

test('update submit works as contributor', function () {
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

    expect($recipe->name)->toBe('Foo');
});

test('edit works as admin', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();

    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.recipes.edit', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeEdit')
        ->where('recipe.name', $recipe->name)
    );
});

test('edit works as contributor', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();

    $this->actingAs($this->contributorUser);
    $response = $this->get(route('admin.recipes.edit', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeEdit')
        ->where('recipe.name', $recipe->name)
    );
});

test('create works as admin', function () {
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
});

test('create works as contributor', function () {
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
});

test('destroy submit works as admin', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->delete(route('admin.recipes.destroy', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );

    expect(Recipe::onlyTrashed()->count())->toBe(1);
});

test('destroy submit works as contributor', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();

    $this->actingAs($this->contributorUser);
    $response = $this->followingRedirects()->delete(route('admin.recipes.destroy', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );

    expect(Recipe::onlyTrashed()->count())->toBe(1);
});

test('restore submit works as admin', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();
    $recipe->delete();

    expect(Recipe::onlyTrashed()->count())->toBe(1);

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->put(route('admin.recipes.restore', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );

    expect(Recipe::onlyTrashed()->count())->toBe(0);
});

test('restore submit works as contributor', function () {
    Recipe::factory()->count(3)->create();
    $recipe = Recipe::first();
    $recipe->delete();

    expect(Recipe::onlyTrashed()->count())->toBe(1);

    $this->actingAs($this->contributorUser);
    $response = $this->followingRedirects()->put(route('admin.recipes.restore', ['recipe' => $recipe]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Recipe/RecipeIndex')
        ->has('recipes', 3)
    );

    expect(Recipe::onlyTrashed()->count())->toBe(0);
});

test('index does not display view to nonadmin', function () {
    Recipe::factory()->count(3)->create();
    $this->actingAs($this->nonAdminUser);
    $response = $this->followingRedirects()->get(route('admin.recipes.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
    );
});
