<?php

namespace Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Models\Image;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdminImageControllerTest extends TestCase
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
        public function recipe_edit_can_upload(): void
        {
            Storage::fake();
            $recipe = Recipe::factory()->create();
            $this->actingAs($this->adminUser);

            $response = $this->json('POST', route("admin.recipes.images.store", ["recipe" => $recipe]), [
                'images' => [UploadedFile::fake()->image('avatar.jpg')]
            ]);

            $response->assertStatus(204);
            $this->assertSame(1, $recipe->media()->count());
        }

        #[Test]
        public function image_can_be_made_hero(): void
        {
            Storage::fake();
            $recipe = Recipe::factory()->create();
            foreach (range(1, 3) as $index) {
                $recipe
                    ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
                    ->preservingOriginal()
                    ->toMediaCollection();
            }

            $this->assertSame(3, $recipe->media()->count());
            $this->assertSame(0, $recipe->getMedia('hero')->count());

            $this->actingAs($this->adminUser);

            $response = $this->followingRedirects()
                ->json('POST', route("admin.images.make_hero", ["media" => $recipe->getFirstMedia()]));

            $response->assertOk();
            $recipe->refresh();

            $this->assertSame(3, $recipe->media()->count());
            $this->assertSame(1, $recipe->getMedia('hero')->count());
        }

        #[Test]
        public function image_can_be_destroyed(): void
        {
            Storage::fake();
            $recipe = Recipe::factory()->create();
            foreach (range(1, 3) as $index) {
                $recipe
                    ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
                    ->preservingOriginal()
                    ->toMediaCollection();
            }

            $this->assertSame(3, $recipe->media()->count());

            $this->actingAs($this->adminUser);

            $response = $this->followingRedirects()
                ->json('DELETE', route("admin.images.destroy", ["media" => $recipe->getFirstMedia()]));

            $response->assertOk();
            $recipe->refresh();

            $this->assertSame(2, $recipe->media()->count());
        }
}
