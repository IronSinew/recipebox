<?php

use App\Enums\UserRoleEnum;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
    $this->contributorUser = User::factory()->create(['role' => UserRoleEnum::CONTRIBUTOR]);
    $this->nonAdminUser = User::factory()->create(['role' => null]);
});

test('index displays view to admin', function () {
    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/User/UserIndex')
        ->has('users', 3)
    );
});

test('index does not display view to contributor', function () {
    $this->actingAs($this->contributorUser);
    $response = $this->followingRedirects()->get(route('admin.users.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
    );
});

test('store submit works as admin', function () {
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
});

test('update submit works as admin', function () {
    $user = User::factory()->create();

    $this->actingAs($this->adminUser);

    $response = $this->followingRedirects()->put(route('admin.users.update', ['user' => $user]), [
        'name' => 'Foo1',
        'email' => 'foo1@bar.com',
        'role' => UserRoleEnum::CONTRIBUTOR->value,
    ]);

    $response->assertOk();
    $user->refresh();

    expect($user->name)->toBe('Foo1');
});

test('edit works as admin', function () {
    $user = User::factory()->create();

    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.users.edit', ['user' => $user]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/User/UserEdit')
        ->where('user.name', $user->name)
    );
});

test('create works as admin', function () {
    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.users.create'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/User/UserEdit')
        ->missing('user.name')
        ->missing('user.id')
    );
});

test('destroy submit works as admin', function () {
    $user = User::factory()->create();

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->delete(route('admin.users.destroy', ['user' => $user]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/User/UserIndex')
        ->has('users', 3)
    );
});

test('index does not display view to nonadmin', function () {
    $this->actingAs($this->nonAdminUser);
    $response = $this->followingRedirects()->get(route('admin.users.index'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
    );
});
