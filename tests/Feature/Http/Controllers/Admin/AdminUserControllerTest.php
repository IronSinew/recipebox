<?php

namespace Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminUserControllerTest extends TestCase
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
        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/UserIndex')
            ->has('users', 3)
        );
    }

    #[Test]
    public function index_does_not_display_view_to_contributor(): void
    {
        $this->actingAs($this->contributorUser);
        $response = $this->followingRedirects()->get(route('admin.users.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
    }

    #[Test]
    public function store_submit_works_as_admin(): void
    {
        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->post(route('admin.users.store'), [
            'name' => 'Foo',
            'email' => 'foo@bar.com',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
            'role' => UserRoleEnum::ADMIN->value,
        ]);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/UserIndex')
            ->has('users', 4)
        );
    }

    #[Test]
    public function update_submit_works_as_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->adminUser);

        $response = $this->followingRedirects()->put(route('admin.users.update', ['user' => $user]), [
            'name' => 'Foo1',
            'email' => 'foo1@bar.com',
            'role' => UserRoleEnum::CONTRIBUTOR->value,
        ]);

        $response->assertOk();
        $user->refresh();

        $this->assertSame('Foo1', $user->name);
    }

    #[Test]
    public function edit_works_as_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.users.edit', ['user' => $user]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/UserEdit')
            ->where('user.name', $user->name)
        );
    }

    #[Test]
    public function create_works_as_admin(): void
    {
        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.users.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/UserEdit')
            ->missing('user.name')
            ->missing('user.id')
        );
    }

    #[Test]
    public function destroy_submit_works_as_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->delete(route('admin.users.destroy', ['user' => $user]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/User/UserIndex')
            ->has('users', 3)
        );
    }

    #[Test]
    public function index_does_not_display_view_to_nonadmin(): void
    {
        $this->actingAs($this->nonAdminUser);
        $response = $this->followingRedirects()->get(route('admin.users.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
    }
}
