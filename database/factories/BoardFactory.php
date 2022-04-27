<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;
    private static int $row = 1;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $row = self::$row++;
        if ($row == 3)
        {
            self::$row = 1;
        }
        return [
            'game_id' => fn() => Board::factory(),
            'row' => $row,
            'column_1' => $this->faker->randomElement(['x', 'y']),
            'column_2' => $this->faker->randomElement(['x', 'y']),
            'column_3' => $this->faker->randomElement(['x', 'y'])
        ];
    }
}
