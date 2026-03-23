<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameElement;
use App\Models\Pictogram;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEmparejar();
        $this->seedSecuencia();
        $this->seedEmocion();
    }

    /**
     * Seed matching (emparejar) games.
     */
    private function seedEmparejar(): void
    {
        $games = [
            [
                'name' => 'Empareja los Animales',
                'description' => 'Une cada animal con su nombre',
                'level' => 1,
                'pictograms' => ['Perro', 'Gato', 'Pájaro', 'Pez'],
            ],
            [
                'name' => 'Empareja las Emociones',
                'description' => 'Une cada emoción con su nombre',
                'level' => 2,
                'pictograms' => ['Feliz', 'Triste', 'Enojado', 'Asustado'],
            ],
            [
                'name' => 'Empareja las Actividades',
                'description' => 'Une cada actividad con su nombre',
                'level' => 2,
                'pictograms' => ['Leer', 'Dibujar', 'Correr', 'Nadar'],
            ],
        ];

        foreach ($games as $gameData) {
            $game = Game::create([
                'name' => $gameData['name'],
                'description' => $gameData['description'],
                'type' => 'emparejar',
                'level' => $gameData['level'],
                'is_active' => true,
            ]);

            foreach ($gameData['pictograms'] as $pictogramName) {
                $pictogram = Pictogram::where('name', $pictogramName)->first();
                if ($pictogram) {
                    GameElement::create([
                        'game_id' => $game->id,
                        'pictogram_id' => $pictogram->id,
                    ]);
                }
            }
        }
    }

    /**
     * Seed sequence (secuencia) games.
     */
    private function seedSecuencia(): void
    {
        $games = [
            [
                'name' => 'Rutina de la Mañana',
                'description' => 'Ordena los pasos para empezar el día',
                'level' => 1,
                'steps' => ['Despertarse', 'Ir al baño', 'Lavarse los dientes', 'Vestirse', 'Desayunar'],
            ],
            [
                'name' => 'Lavarse las Manos',
                'description' => 'Ordena los pasos para lavarse las manos',
                'level' => 1,
                'steps' => ['Ir al baño', 'Lavarse las manos', 'Bañarse', 'Peinarse'],
            ],
            [
                'name' => 'Prepararse para Dormir',
                'description' => 'Ordena los pasos para ir a dormir',
                'level' => 2,
                'steps' => ['Cenar', 'Lavarse los dientes', 'Bañarse', 'Vestirse'],
            ],
        ];

        foreach ($games as $gameData) {
            $game = Game::create([
                'name' => $gameData['name'],
                'description' => $gameData['description'],
                'type' => 'secuencia',
                'level' => $gameData['level'],
                'is_active' => true,
            ]);

            foreach ($gameData['steps'] as $order => $pictogramName) {
                $pictogram = Pictogram::where('name', $pictogramName)->first();
                if ($pictogram) {
                    GameElement::create([
                        'game_id' => $game->id,
                        'pictogram_id' => $pictogram->id,
                        'sequence_order' => $order + 1,
                    ]);
                }
            }
        }
    }

    /**
     * Seed emotion (emocion) games.
     * The first element (sequence_order = 1) is the correct answer.
     * Other elements are distractors.
     */
    private function seedEmocion(): void
    {
        $games = [
            [
                'name' => '¿Cómo te sientes?',
                'description' => 'Escuchas música alegre y empiezas a bailar',
                'level' => 1,
                'correct' => 'Feliz',
                'distractors' => ['Triste', 'Enojado', 'Asustado'],
            ],
            [
                'name' => 'Situación Difícil',
                'description' => 'Se rompe tu juguete favorito',
                'level' => 1,
                'correct' => 'Triste',
                'distractors' => ['Feliz', 'Emocionado', 'Cansado'],
            ],
            [
                'name' => 'Un Susto',
                'description' => 'Ves una araña grande en tu cuarto',
                'level' => 2,
                'correct' => 'Asustado',
                'distractors' => ['Feliz', 'Triste', 'Cansado'],
            ],
            [
                'name' => '¡Sorpresa!',
                'description' => 'Tus amigos organizan una fiesta de cumpleaños para ti',
                'level' => 2,
                'correct' => 'Emocionado',
                'distractors' => ['Triste', 'Nervioso', 'Cansado'],
            ],
            [
                'name' => 'La Espera',
                'description' => 'Tienes que exponer enfrente de toda la clase',
                'level' => 3,
                'correct' => 'Nervioso',
                'distractors' => ['Feliz', 'Sorprendido', 'Cansado'],
            ],
        ];

        foreach ($games as $gameData) {
            $game = Game::create([
                'name' => $gameData['name'],
                'description' => $gameData['description'],
                'type' => 'emocion',
                'level' => $gameData['level'],
                'is_active' => true,
            ]);

            // Correct answer first (sequence_order = 1)
            $correctPictogram = Pictogram::where('name', $gameData['correct'])->first();
            if ($correctPictogram) {
                GameElement::create([
                    'game_id' => $game->id,
                    'pictogram_id' => $correctPictogram->id,
                    'sequence_order' => 1,
                ]);
            }

            // Distractors
            $order = 2;
            foreach ($gameData['distractors'] as $pictogramName) {
                $pictogram = Pictogram::where('name', $pictogramName)->first();
                if ($pictogram) {
                    GameElement::create([
                        'game_id' => $game->id,
                        'pictogram_id' => $pictogram->id,
                        'sequence_order' => $order++,
                    ]);
                }
            }
        }
    }
}
