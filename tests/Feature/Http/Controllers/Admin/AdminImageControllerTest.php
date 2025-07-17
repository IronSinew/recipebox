<?php

use App\Enums\UserRoleEnum;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
    $this->nonAdminUser = User::factory()->create();
});

test('recipe edit can upload', function () {
    Storage::fake();
    $recipe = Recipe::factory()->create();
    $this->actingAs($this->adminUser);

    $response = $this->json('POST', route('admin.recipes.images.store', ['recipe' => $recipe]), [
        'images' => [UploadedFile::fake()->image('avatar.jpg')],
    ]);

    $response->assertStatus(204);
    expect($recipe->media()->count())->toBe(1);
});

test('image can be made hero', function () {
    Storage::fake();
    $recipe = Recipe::factory()->create();
    foreach (range(1, 3) as $index) {
        $recipe
            ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
            ->preservingOriginal()
            ->toMediaCollection();
    }

    expect($recipe->media()->count())->toBe(3);
    expect($recipe->getMedia('hero')->count())->toBe(0);

    $this->actingAs($this->adminUser);

    $response = $this->followingRedirects()
        ->json('POST', route('admin.images.make_hero', ['media' => $recipe->getFirstMedia()]));

    $response->assertOk();
    $recipe->refresh();

    expect($recipe->media()->count())->toBe(3);
    expect($recipe->getMedia('hero')->count())->toBe(1);
});

test('image can be destroyed', function () {
    Storage::fake();
    $recipe = Recipe::factory()->create();
    foreach (range(1, 3) as $index) {
        $recipe
            ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
            ->preservingOriginal()
            ->toMediaCollection();
    }

    expect($recipe->media()->count())->toBe(3);

    $this->actingAs($this->adminUser);

    $response = $this->followingRedirects()
        ->json('DELETE', route('admin.images.destroy', ['media' => $recipe->getFirstMedia()]));

    $response->assertOk();
    $recipe->refresh();

    expect($recipe->media()->count())->toBe(2);
});
