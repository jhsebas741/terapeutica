<?php

use App\Models\Pictogram;
use App\Models\Routine;
use App\Models\RoutineStep;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);
});

it('displays routines index page', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');
    Routine::factory()->count(3)->create(['patient_id' => $patient->id]);

    $this->get('/admin/routines')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/routines/Index')
            ->has('paginatedRoutines.data', 3)
            ->has('patients'));
});

it('displays the create routine page', function () {
    $this->get('/admin/routines/create')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/routines/Create')
            ->has('patients')
            ->has('categories'));
});

it('stores a new routine with steps', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');
    $pictogram = Pictogram::factory()->create();

    $this->post('/admin/routines', [
        'patient_id' => $patient->id,
        'name' => 'Rutina Matutina',
        'description' => 'Pasos de la mañana',
        'steps' => [
            ['pictogram_id' => $pictogram->id],
        ],
    ])->assertRedirect('/admin/routines');

    $this->assertDatabaseHas('routines', ['name' => 'Rutina Matutina']);
    $this->assertDatabaseHas('routine_steps', [
        'pictogram_id' => $pictogram->id,
        'order' => 1,
    ]);
});

it('validates required fields on store', function () {
    $this->post('/admin/routines', [])
        ->assertSessionHasErrors(['patient_id', 'name', 'steps']);
});

it('validates steps must have at least one entry', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');

    $this->post('/admin/routines', [
        'patient_id' => $patient->id,
        'name' => 'Test',
        'steps' => [],
    ])->assertSessionHasErrors(['steps']);
});

it('displays the edit routine page', function () {
    $routine = Routine::factory()->create([
        'patient_id' => User::factory()->create()->id,
    ]);
    RoutineStep::factory()->create(['routine_id' => $routine->id]);

    $this->get("/admin/routines/{$routine->id}/edit")
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/routines/Edit')
            ->has('routine')
            ->has('patients')
            ->has('categories'));
});

it('updates a routine and syncs steps', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');
    $routine = Routine::factory()->create(['patient_id' => $patient->id, 'name' => 'Old']);
    RoutineStep::factory()->create(['routine_id' => $routine->id]);

    $newPictogram = Pictogram::factory()->create();

    $this->put("/admin/routines/{$routine->id}", [
        'patient_id' => $patient->id,
        'name' => 'Updated',
        'steps' => [
            ['pictogram_id' => $newPictogram->id],
        ],
    ])->assertRedirect('/admin/routines');

    $this->assertDatabaseHas('routines', ['name' => 'Updated']);
    expect($routine->fresh()->routineSteps)->toHaveCount(1);
});

it('deletes a routine', function () {
    $routine = Routine::factory()->create([
        'patient_id' => User::factory()->create()->id,
    ]);

    $this->delete("/admin/routines/{$routine->id}")
        ->assertRedirect('/admin/routines');

    $this->assertDatabaseMissing('routines', ['id' => $routine->id]);
});

it('filters routines by search', function () {
    $patient = User::factory()->create();
    $patient->assignRole('paciente');
    Routine::factory()->create(['patient_id' => $patient->id, 'name' => 'Rutina Mañana']);
    Routine::factory()->create(['patient_id' => $patient->id, 'name' => 'Rutina Noche']);

    $this->get('/admin/routines?filter[search]=Mañana')
        ->assertInertia(fn ($page) => $page
            ->has('paginatedRoutines.data', 1));
});

it('filters routines by patient', function () {
    $p1 = User::factory()->create();
    $p1->assignRole('paciente');
    $p2 = User::factory()->create();
    $p2->assignRole('paciente');
    Routine::factory()->count(2)->create(['patient_id' => $p1->id]);
    Routine::factory()->create(['patient_id' => $p2->id]);

    $this->get("/admin/routines?filter[patient_id]={$p1->id}")
        ->assertInertia(fn ($page) => $page
            ->has('paginatedRoutines.data', 2));
});

it('ignores patient_id filter when value is all', function () {
    $p1 = User::factory()->create();
    $p1->assignRole('paciente');
    $p2 = User::factory()->create();
    $p2->assignRole('paciente');
    Routine::factory()->count(2)->create(['patient_id' => $p1->id]);
    Routine::factory()->create(['patient_id' => $p2->id]);

    $this->get('/admin/routines?filter[patient_id]=all')
        ->assertInertia(fn ($page) => $page
            ->has('paginatedRoutines.data', 3));
});
