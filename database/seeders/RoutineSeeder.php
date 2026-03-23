<?php

namespace Database\Seeders;

use App\Models\Pictogram;
use App\Models\Routine;
use App\Models\RoutineStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoutineSeeder extends Seeder
{
    public function run(): void
    {
        $patient = User::role('paciente')->first();

        if (! $patient) {
            return;
        }

        $routines = [
            [
                'name' => 'Rutina de la Mañana',
                'description' => 'Actividades para comenzar bien el día',
                'steps' => ['Despertarse', 'Apagar la alarma', 'Ir al baño', 'Lavarse los dientes', 'Bañarse', 'Vestirse', 'Desayunar', 'Ir al colegio'],
            ],
            [
                'name' => 'Rutina de Higiene',
                'description' => 'Cuidado personal diario',
                'steps' => ['Lavarse las manos', 'Lavarse los dientes', 'Peinarse', 'Bañarse'],
            ],
            [
                'name' => 'Después del Colegio',
                'description' => 'Actividades al llegar a casa',
                'steps' => ['Merendar', 'Lavarse las manos', 'Leer', 'Jugar', 'Cenar', 'Lavarse los dientes'],
            ],
        ];

        foreach ($routines as $routineData) {
            $routine = Routine::create([
                'patient_id' => $patient->id,
                'name' => $routineData['name'],
                'description' => $routineData['description'],
                'is_active' => true,
            ]);

            foreach ($routineData['steps'] as $order => $pictogramName) {
                $pictogram = Pictogram::where('name', $pictogramName)->first();

                if ($pictogram) {
                    RoutineStep::create([
                        'routine_id' => $routine->id,
                        'pictogram_id' => $pictogram->id,
                        'order' => $order + 1,
                    ]);
                }
            }
        }
    }
}
