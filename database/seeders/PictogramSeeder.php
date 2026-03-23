<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pictogram;
use Illuminate\Database\Seeder;

class PictogramSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Rutina Matutina' => [
                ['name' => 'Despertarse', 'description' => 'Levantarse de la cama', 'icon_text' => '🌅'],
                ['name' => 'Apagar la alarma', 'description' => 'Silenciar el despertador', 'icon_text' => '⏰'],
                ['name' => 'Estirarse', 'description' => 'Hacer ejercicios de estiramiento', 'icon_text' => '🧘'],
                ['name' => 'Ir al baño', 'description' => 'Usar el servicio', 'icon_text' => '🚽'],
                ['name' => 'Vestirse', 'description' => 'Ponerse la ropa', 'icon_text' => '👕'],
                ['name' => 'Hacer la cama', 'description' => 'Ordenar las cobijas', 'icon_text' => '🛏️'],
                ['name' => 'Ir al colegio', 'description' => 'Salir hacia la escuela', 'icon_text' => '🎒'],
            ],
            'Higiene Personal' => [
                ['name' => 'Lavarse los dientes', 'description' => 'Cepillarse los dientes', 'icon_text' => '🦷'],
                ['name' => 'Lavarse las manos', 'description' => 'Lavar las manos con jabón', 'icon_text' => '🙌'],
                ['name' => 'Bañarse', 'description' => 'Tomar una ducha', 'icon_text' => '🚿'],
                ['name' => 'Peinarse', 'description' => 'Cepillar el cabello', 'icon_text' => '💈'],
                ['name' => 'Cortarse las uñas', 'description' => 'Recortar las uñas', 'icon_text' => '✂️'],
            ],
            'Alimentación' => [
                ['name' => 'Desayunar', 'description' => 'Comer el desayuno', 'icon_text' => '🍳'],
                ['name' => 'Almorzar', 'description' => 'Comer el almuerzo', 'icon_text' => '🍱'],
                ['name' => 'Merendar', 'description' => 'Comer una merienda', 'icon_text' => '🍎'],
                ['name' => 'Cenar', 'description' => 'Comer la cena', 'icon_text' => '🍽️'],
                ['name' => 'Beber agua', 'description' => 'Tomar agua', 'icon_text' => '💧'],
                ['name' => 'Beber leche', 'description' => 'Tomar un vaso de leche', 'icon_text' => '🥛'],
                ['name' => 'Comer fruta', 'description' => 'Comer una fruta', 'icon_text' => '🍇'],
            ],
            'Emociones' => [
                ['name' => 'Feliz', 'description' => 'Sentirse contento y alegre', 'icon_text' => '😊'],
                ['name' => 'Triste', 'description' => 'Sentirse con pena o dolor', 'icon_text' => '😢'],
                ['name' => 'Enojado', 'description' => 'Sentirse con rabia o fastidio', 'icon_text' => '😠'],
                ['name' => 'Asustado', 'description' => 'Sentirse con miedo', 'icon_text' => '😨'],
                ['name' => 'Sorprendido', 'description' => 'Sentirse sorprendido', 'icon_text' => '😲'],
                ['name' => 'Cansado', 'description' => 'Sentirse sin energía', 'icon_text' => '😴'],
                ['name' => 'Emocionado', 'description' => 'Sentirse muy ilusionado', 'icon_text' => '🤩'],
                ['name' => 'Nervioso', 'description' => 'Sentirse ansioso o inquieto', 'icon_text' => '😰'],
            ],
            'Animales' => [
                ['name' => 'Perro', 'description' => 'Un perro', 'icon_text' => '🐶'],
                ['name' => 'Gato', 'description' => 'Un gato', 'icon_text' => '🐱'],
                ['name' => 'Pájaro', 'description' => 'Un pájaro', 'icon_text' => '🐦'],
                ['name' => 'Pez', 'description' => 'Un pez', 'icon_text' => '🐟'],
                ['name' => 'Conejo', 'description' => 'Un conejo', 'icon_text' => '🐰'],
                ['name' => 'Oso', 'description' => 'Un oso', 'icon_text' => '🐻'],
                ['name' => 'León', 'description' => 'Un león', 'icon_text' => '🦁'],
                ['name' => 'Elefante', 'description' => 'Un elefante', 'icon_text' => '🐘'],
            ],
            'Escuela' => [
                ['name' => 'Leer', 'description' => 'Leer un libro', 'icon_text' => '📖'],
                ['name' => 'Escribir', 'description' => 'Escribir con un lápiz', 'icon_text' => '✏️'],
                ['name' => 'Dibujar', 'description' => 'Hacer un dibujo', 'icon_text' => '🎨'],
                ['name' => 'Escuchar', 'description' => 'Prestar atención al profesor', 'icon_text' => '👂'],
                ['name' => 'Guardar silencio', 'description' => 'Estar en silencio', 'icon_text' => '🤫'],
                ['name' => 'Levantar la mano', 'description' => 'Pedir la palabra', 'icon_text' => '✋'],
            ],
            'Deportes y Juegos' => [
                ['name' => 'Correr', 'description' => 'Correr o trotar', 'icon_text' => '🏃'],
                ['name' => 'Saltar', 'description' => 'Dar saltos', 'icon_text' => '🦘'],
                ['name' => 'Jugar fútbol', 'description' => 'Jugar al fútbol', 'icon_text' => '⚽'],
                ['name' => 'Nadar', 'description' => 'Nadar en la piscina', 'icon_text' => '🏊'],
                ['name' => 'Bailar', 'description' => 'Bailar con música', 'icon_text' => '💃'],
                ['name' => 'Jugar', 'description' => 'Jugar en el parque', 'icon_text' => '🎮'],
            ],
        ];

        foreach ($data as $categoryName => $pictograms) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($pictograms as $i => $picto) {
                Pictogram::create([
                    'category_id' => $category->id,
                    'name' => $picto['name'],
                    'description' => $picto['description'],
                    'icon_text' => $picto['icon_text'],
                    'difficulty_level' => min(3, intdiv($i, 2) + 1),
                    'is_active' => true,
                ]);
            }
        }
    }
}
