<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rutina Matutina',
                'description' => 'Actividades para comenzar el día',
                'icon_emoji' => '🌅',
                'color_hex' => '#FCD34D',
            ],
            [
                'name' => 'Higiene Personal',
                'description' => 'Cuidado e higiene diaria',
                'icon_emoji' => '🧼',
                'color_hex' => '#60A5FA',
            ],
            [
                'name' => 'Alimentación',
                'description' => 'Comidas y bebidas',
                'icon_emoji' => '🍽️',
                'color_hex' => '#34D399',
            ],
            [
                'name' => 'Emociones',
                'description' => 'Sentimientos y estados emocionales',
                'icon_emoji' => '😊',
                'color_hex' => '#F87171',
            ],
            [
                'name' => 'Animales',
                'description' => 'Animales y mascotas',
                'icon_emoji' => '🐾',
                'color_hex' => '#A78BFA',
            ],
            [
                'name' => 'Escuela',
                'description' => 'Actividades y materiales escolares',
                'icon_emoji' => '🏫',
                'color_hex' => '#FB923C',
            ],
            [
                'name' => 'Deportes y Juegos',
                'description' => 'Actividades físicas y recreativas',
                'icon_emoji' => '⚽',
                'color_hex' => '#2DD4BF',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
