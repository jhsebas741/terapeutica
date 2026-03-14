<?php

use App\Models\Category;
use App\Models\Pictogram;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    $this->actingAs($this->admin);
    Storage::fake('public');
});

it('displays pictograms index page grouped by category', function () {
    $cat = Category::factory()->create();
    Pictogram::factory()->count(3)->create(['category_id' => $cat->id]);

    $this->get('/admin/pictograms')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/pictograms/Index')
            ->has('groupedPictograms')
            ->has('categories'));
});

it('displays the create pictogram page', function () {
    $this->get('/admin/pictograms/create')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/pictograms/Create')
            ->has('categories'));
});

it('stores a new pictogram with image', function () {
    $category = Category::factory()->create();
    $image = UploadedFile::fake()->create('pictogram.jpg', 100, 'image/jpeg');

    $this->post('/admin/pictograms', [
        'category_id' => $category->id,
        'name' => 'Feliz',
        'description' => 'Cara feliz',
        'image' => $image,
        'difficulty_level' => 1,
        'is_active' => true,
    ])->assertRedirect('/admin/pictograms');

    $this->assertDatabaseHas('pictograms', ['name' => 'Feliz']);

    $pictogram = Pictogram::where('name', 'Feliz')->first();
    Storage::disk('public')->assertExists($pictogram->image_url);
});

it('stores a new pictogram with icon_text', function () {
    $category = Category::factory()->create();

    $this->post('/admin/pictograms', [
        'category_id' => $category->id,
        'name' => 'Sonrisa',
        'icon_text' => '😊',
        'difficulty_level' => 2,
        'is_active' => true,
    ])->assertRedirect('/admin/pictograms');

    $this->assertDatabaseHas('pictograms', [
        'name' => 'Sonrisa',
        'icon_text' => '😊',
    ]);
});

it('validates either image or icon_text is required on store', function () {
    $category = Category::factory()->create();

    $this->post('/admin/pictograms', [
        'category_id' => $category->id,
        'name' => 'Test',
        'difficulty_level' => 1,
    ])->assertSessionHasErrors(['image', 'icon_text']);
});

it('validates required fields on store', function () {
    $this->post('/admin/pictograms', [])
        ->assertSessionHasErrors(['name', 'difficulty_level', 'category_id']);
});

it('displays the edit pictogram page', function () {
    $pictogram = Pictogram::factory()->create();

    $this->get("/admin/pictograms/{$pictogram->id}/edit")
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/catalog/pictograms/Edit')
            ->has('pictogram')
            ->has('categories'));
});

it('updates a pictogram', function () {
    $pictogram = Pictogram::factory()->create(['name' => 'Old']);

    $this->put("/admin/pictograms/{$pictogram->id}", [
        'category_id' => $pictogram->category_id,
        'name' => 'Updated',
        'difficulty_level' => 3,
        'icon_text' => '🎉',
    ])->assertRedirect('/admin/pictograms');

    $this->assertDatabaseHas('pictograms', ['name' => 'Updated', 'difficulty_level' => 3]);
});

it('soft deletes a pictogram', function () {
    $pictogram = Pictogram::factory()->create();

    $this->delete("/admin/pictograms/{$pictogram->id}")
        ->assertRedirect('/admin/pictograms');

    $this->assertSoftDeleted('pictograms', ['id' => $pictogram->id]);
});

it('filters pictograms by search', function () {
    Pictogram::factory()->create(['name' => 'Feliz']);
    Pictogram::factory()->create(['name' => 'Triste']);

    $this->get('/admin/pictograms?filter[search]=Feliz')
        ->assertInertia(fn ($page) => $page
            ->where('groupedPictograms', fn ($groups) => collect($groups)
                ->flatMap(fn ($g) => $g['pictograms'])
                ->count() === 1));
});

it('filters pictograms by category', function () {
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();
    Pictogram::factory()->count(2)->create(['category_id' => $cat1->id]);
    Pictogram::factory()->create(['category_id' => $cat2->id]);

    $this->get("/admin/pictograms?filter[category_id]={$cat1->id}")
        ->assertInertia(fn ($page) => $page
            ->where('groupedPictograms', fn ($groups) => collect($groups)
                ->flatMap(fn ($g) => $g['pictograms'])
                ->count() === 2));
});

it('ignores category_id filter when value is all', function () {
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();
    Pictogram::factory()->count(2)->create(['category_id' => $cat1->id]);
    Pictogram::factory()->create(['category_id' => $cat2->id]);

    $this->get('/admin/pictograms?filter[category_id]=all')
        ->assertInertia(fn ($page) => $page
            ->where('groupedPictograms', fn ($groups) => collect($groups)
                ->flatMap(fn ($g) => $g['pictograms'])
                ->count() === 3));
});
