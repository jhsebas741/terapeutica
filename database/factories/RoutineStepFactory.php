<?php

namespace Database\Factories;

use App\Models\Pictogram;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoutineStep>
 */
class RoutineStepFactory extends Factory
{
    public function definition(): array
    {
        return [
            'routine_id' => Routine::factory(),
            'pictogram_id' => Pictogram::factory(),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
