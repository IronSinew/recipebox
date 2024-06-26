<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\LabelController as AdminLabelController;
use App\Http\Controllers\Admin\RecipeController as AdminRecipeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AllRecipeListController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryListController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SearchSimpleController;
use App\Http\Middleware\HasAdminAreaAccess;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::prefix('/categories')->name('category.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/list', CategoryListController::class)->name('list');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
});

Route::get('/labels', [LabelController::class, 'index'])->name('label.index');
Route::get('/labels/{label}', [LabelController::class, 'show'])->name('label.show');

Route::get('/recipes', AllRecipeListController::class)->name('recipe.all');
Route::get('/recipe/{recipe}', RecipeController::class)->name('recipe.show');
Route::post('/search-simple', SearchSimpleController::class)->name('search.simple');

Route::prefix('/admin')->name('admin.')->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    HasAdminAreaAccess::class])
    ->group(function () {
        /**
         * Admin only, not contributors
         */
        Route::middleware(IsAdmin::class)->group(function () {
            // labels
            Route::post('/labels/order', [AdminLabelController::class, 'setNewOrder'])->name('labels.set_order');
            Route::put('/labels/{label}/restore', [AdminLabelController::class, 'restore'])
                ->withTrashed()
                ->name('labels.restore');
            Route::resource('labels', AdminLabelController::class)->except('show', 'create');

            // categories
            Route::post('/categories/order', [AdminCategoryController::class, 'setNewOrder'])->name('categories.set_order');
            Route::put('/categories/{category}/restore', [AdminCategoryController::class, 'restore'])
                ->withTrashed()
                ->name('categories.restore');
            Route::resource('categories', AdminCategoryController::class)->except('show', 'create');

            // users
            Route::resource('users', UserController::class)->except('show');
        });

        /**
         * Contributor-allowed
         */
        // recipes
        Route::put('/recipes/{recipe}/restore', [AdminRecipeController::class, 'restore'])->withTrashed()->name('recipes.restore');
        Route::post('/recipes/{recipe}/image', [AdminRecipeController::class, 'imageStore'])->withTrashed()->name('recipes.images.store');
        Route::resource('recipes', AdminRecipeController::class)->except('show')->withTrashed(['edit']);

        // images
        Route::delete('/images/{media}', [ImageController::class, 'destroy'])->name('images.destroy');
        Route::post('/images/{media}/hero', [ImageController::class, 'makeHero'])->name('images.make_hero');
    });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
});
