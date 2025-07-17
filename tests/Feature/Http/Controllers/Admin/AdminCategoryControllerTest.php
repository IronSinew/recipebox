<?php

use App\Enums\UserRoleEnum;
use App\Models\Category;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
    $this->nonAdminUser = User::factory()->create();
});

test('index displays view to admin', function () {
    Category::factory()->count(3)->create();
    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.categories.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Category/CategoryIndex')
        ->has('categories', 3)
    );
});

test('store submit works as admin', function () {
    Category::factory()->count(3)->create();
    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->post(route('admin.categories.store'), ['name' => 'Foo']);

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Category/CategoryIndex')
        ->has('categories', 4)
    );
});

test('destroy submit works as admin', function () {
    Category::factory()->count(3)->create();
    $category = Category::first();

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->delete(route('admin.categories.destroy', ['category' => $category]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Category/CategoryIndex')
        ->has('categories', 3)
    );

    expect(Category::onlyTrashed()->count())->toBe(1);
});

test('restore submit works as admin', function () {
    Category::factory()->count(3)->create();
    $category = Category::first();
    $category->delete();

    expect(Category::onlyTrashed()->count())->toBe(1);

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->put(route('admin.categories.restore', ['category' => $category]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Category/CategoryIndex')
        ->has('categories', 3)
    );

    expect(Category::onlyTrashed()->count())->toBe(0);
});

test('index does not display view to nonadmin', function () {
    Category::factory()->count(3)->create();
    $this->actingAs($this->nonAdminUser);
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect('/');
});
