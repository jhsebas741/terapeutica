<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameElement>
 */
class GameElementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => \App\Models\Game::factory(),
            'pictogram_id' => \App\Models\Pictogram::factory(),
            'situation_text' => null,
            'sequence_order' => null,
        ];
    }
}
