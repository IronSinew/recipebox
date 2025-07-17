<?php

use App\Enums\UserRoleEnum;
use App\Models\Label;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
    $this->nonAdminUser = User::factory()->create();
});

test('index displays view to admin', function () {
    Label::factory()->count(3)->create();
    $this->actingAs($this->adminUser);
    $response = $this->get(route('admin.labels.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Label/LabelIndex')
        ->has('labels', 3)
    );
});

test('store submit works as admin', function () {
    Label::factory()->count(3)->create();
    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->post(route('admin.labels.store'), ['name' => 'Foo']);

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Label/LabelIndex')
        ->has('labels', 4)
    );
});

test('destroy submit works as admin', function () {
    Label::factory()->count(3)->create();
    $label = Label::first();

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->delete(route('admin.labels.destroy', ['label' => $label]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Label/LabelIndex')
        ->has('labels', 3)
    );

    expect(Label::onlyTrashed()->count())->toBe(1);
});

test('restore submit works as admin', function () {
    Label::factory()->count(3)->create();
    $label = Label::first();
    $label->delete();

    expect(Label::onlyTrashed()->count())->toBe(1);

    $this->actingAs($this->adminUser);
    $response = $this->followingRedirects()->put(route('admin.labels.restore', ['label' => $label]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Admin/Label/LabelIndex')
        ->has('labels', 3)
    );

    expect(Label::onlyTrashed()->count())->toBe(0);
});

test('index does not display view to nonadmin', function () {
    Label::factory()->count(3)->create();
    $this->actingAs($this->nonAdminUser);
    $response = $this->get(route('admin.labels.index'));

    $response->assertRedirect('/');
});
