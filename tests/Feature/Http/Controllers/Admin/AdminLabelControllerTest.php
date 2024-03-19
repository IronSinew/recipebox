<?php

namespace Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminLabelControllerTest extends TestCase
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
        Label::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->get(route('admin.labels.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Label/LabelIndex')
            ->has('labels', 3)
        );
    }

    #[Test]
    public function store_submit_works_as_admin(): void
    {
        Label::factory()->count(3)->create();
        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->post(route('admin.labels.store'), ['name' => 'Foo']);

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Label/LabelIndex')
            ->has('labels', 4)
        );
    }

    #[Test]
    public function destroy_submit_works_as_admin(): void
    {
        Label::factory()->count(3)->create();
        $label = Label::first();

        $this->actingAs($this->adminUser);
        $response = $this->followingRedirects()->delete(route('admin.labels.destroy', ['label' => $label]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Label/LabelIndex')
            ->has('labels', 2)
        );
    }

    #[Test]
    public function index_does_not_display_view_to_nonadmin(): void
    {
        Label::factory()->count(3)->create();
        $this->actingAs($this->nonAdminUser);
        $response = $this->get(route('admin.labels.index'));

        $response->assertRedirect('/');
    }
}
