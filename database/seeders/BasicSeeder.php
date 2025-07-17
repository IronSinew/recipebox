<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Label;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class BasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Appetizer', 'Main course', 'Side dish', 'Dessert'] as $category) {
            Category::create([
                'name' => $category,
            ]);
        }

        foreach (['Favorite', 'Healthy', 'Holiday', 'One Pan', 'Easy', 'Slow Cooker', 'BBQ', 'Smoker'] as $category) {
            Label::create([
                'name' => $category,
            ]);
        }

        $categories = Category::all();
        $labels = Label::all();
        Recipe::factory()->count(20)->create();

        Recipe::all()->each(function ($recipe) use ($categories, $labels) {
            $recipe->categories()->attach(
                $categories->random(fake()->randomNumber(1, 3))->pluck('id')->toArray()
            );

            $recipe->labels()->attach(
                $labels->random(fake()->randonNumber(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
