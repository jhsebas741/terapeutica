<?php

use App\Models\Category;
use App\Models\Pictogram;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);
});

it('displays categories index page', function () {
    Category::factory()->count(3)->create();

    $this->get('/admin/categories')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/categories/Index')
            ->has('paginatedCategories.data', 3));
});

it('displays the create category page', function () {
    $this->get('/admin/categories/create')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/categories/Create'));
});

it('stores a new category', function () {
    $this->post('/admin/categories', [
        'name' => 'Emociones',
        'description' => 'Pictogramas de emociones',
        'icon_emoji' => '😊',
        'color_hex' => '#FF6B6B',
    ])->assertRedirect('/admin/categories');

    $this->assertDatabaseHas('categories', ['name' => 'Emociones']);
});

it('validates required fields on store', function () {
    $this->post('/admin/categories', [])
        ->assertSessionHasErrors(['name', 'color_hex']);
});

it('validates unique category name', function () {
    Category::factory()->create(['name' => 'Emociones']);

    $this->post('/admin/categories', [
        'name' => 'Emociones',
        'color_hex' => '#FF6B6B',
    ])->assertSessionHasErrors(['name']);
});

it('displays the edit category page', function () {
    $category = Category::factory()->create();

    $this->get("/admin/categories/{$category->id}/edit")
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/categories/Edit')
            ->has('category'));
});

it('updates a category', function () {
    $category = Category::factory()->create(['name' => 'Old']);

    $this->put("/admin/categories/{$category->id}", [
        'name' => 'Updated',
        'color_hex' => '#00FF00',
    ])->assertRedirect('/admin/categories');

    $this->assertDatabaseHas('categories', ['name' => 'Updated']);
});

it('deletes a category without pictograms', function () {
    $category = Category::factory()->create();

    $this->delete("/admin/categories/{$category->id}")
        ->assertRedirect('/admin/categories');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('prevents deleting a category with pictograms', function () {
    $category = Category::factory()->create();
    Pictogram::factory()->create(['category_id' => $category->id]);

    $this->delete("/admin/categories/{$category->id}")
        ->assertRedirect('/admin/categories');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('filters categories by search', function () {
    Category::factory()->create(['name' => 'Emociones']);
    Category::factory()->create(['name' => 'Alimentos']);

    $this->get('/admin/categories?filter[search]=Emo')
        ->assertInertia(fn ($page) => $page
            ->has('paginatedCategories.data', 1));
});
