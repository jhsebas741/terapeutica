<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertAuthenticatedAs;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
});

it('shows setup page if no admin exists', function () {
    get('/setup')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('Setup/Index'));
});

it('redirects setup page to login if admin exists', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    get('/setup')->assertRedirect('/login');
});

it('creates the first admin user and logs them in', function () {
    post('/setup', [
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/admin');

    assertDatabaseHas('users', ['email' => 'admin@test.com']);
    
    $user = User::where('email', 'admin@test.com')->first();
    expect($user->hasRole('admin'))->toBeTrue();
    
    assertAuthenticatedAs($user);
});

it('redirects post setup if admin already exists', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    post('/setup', [
        'name' => 'Hacker',
        'email' => 'hack@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect('/login');

    $this->assertDatabaseMissing('users', ['email' => 'hack@test.com']);
});
