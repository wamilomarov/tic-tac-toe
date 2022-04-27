<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{

    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->unique()->uuid,
            'score_x' => $this->faker->randomDigit(),
            'score_y' => $this->faker->randomDigit(),
            'current_turn' => $this->faker->randomElement(['x', 'y'])
        ];
    }
}
