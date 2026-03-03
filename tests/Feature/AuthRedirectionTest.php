<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
});

it('redirects admin to admin dashboard', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)->get('/dashboard')
        ->assertRedirect('/admin');
});

it('redirects paciente to child dashboard', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');

    actingAs($patient)->get('/dashboard')
        ->assertRedirect('/child');
});
