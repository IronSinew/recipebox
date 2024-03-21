<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'serving' => $this->faker->word(),
            'ingredients' => $this->faker->text(),
            'instructions' => $this->faker->text(),
            'description' => $this->faker->text(),
            'cook_time' => $this->faker->numberBetween(30, 90),
            'prep_time' => $this->faker->numberBetween(5, 30),
            'published_at' => $this->faker->dateTime(),
            'user_id' => User::factory(),
        ];
    }
}
