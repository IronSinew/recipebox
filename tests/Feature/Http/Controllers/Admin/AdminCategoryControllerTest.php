<?php

namespace Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $nonAdminUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $this->nonAdminUser = User::factory()->create();
    }

    #[Test]
    public function index_displays_view_to_admin(): void
    {
        Category::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Category/CategoryIndex')
            ->has('categories', 3)
        );
    }

    #[Test]
    public function store_submit_works_as_admin(): void
    {
        Category::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->post(route('admin.categories.store'), ['name' => 'Foo']);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Category/CategoryIndex')
            ->has('categories', 4)
        );
    }

    #[Test]
    public function destroy_submit_works_as_admin(): void
    {
        Category::factory()->count(3)->create();
        $category = Category::first();

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->delete(route('admin.categories.destroy', ['category' => $category]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Category/CategoryIndex')
            ->has('categories', 3)
        );

        $this->assertSame(1, Category::onlyTrashed()->count());
    }

    #[Test]
    public function restore_submit_works_as_admin(): void
    {
        Category::factory()->count(3)->create();
        $category = Category::first();
        $category->delete();

        $this->assertSame(1, Category::onlyTrashed()->count());

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->put(route('admin.categories.restore', ['category' => $category]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Category/CategoryIndex')
            ->has('categories', 3)
        );

        $this->assertSame(0, Category::onlyTrashed()->count());
    }

    #[Test]
    public function index_does_not_display_view_to_nonadmin(): void
    {
        Category::factory()->count(3)->create();
        $this->actingAs($this->nonAdminUser);
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect('/');
    }
}
