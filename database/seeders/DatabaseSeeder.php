<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
        ]);
        
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => '12345678',
        ])->assignRole('admin');

        $patient = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345678',
            'birth_date' => '2000-01-01',
            'diagnosis' => 'Test Diagnosis',
        ])->assignRole('paciente');
        
        $patient->patientSetting()->create();
        
    }
}
