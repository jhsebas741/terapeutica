<?php

use App\Models\Game;
use App\Models\GameElement;
use App\Models\Pictogram;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'paciente']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

it('can view games index', function () {
    Game::factory()->count(3)->create();

    actingAs($this->admin)->get('/admin/games')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/games/Index')
            ->has('paginatedGames.data', 3)
        );
});

it('can search games', function () {
    Game::factory()->create(['name' => 'Memorama Frutas']);
    Game::factory()->create(['name' => 'Secuencia Lavado']);

    actingAs($this->admin)->get('/admin/games?filter[search]=Memorama')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/games/Index')
            ->has('paginatedGames.data', 1)
        );
});

it('can filter games by type', function () {
    Game::factory()->create(['type' => 'emparejar']);
    Game::factory()->create(['type' => 'secuencia']);

    actingAs($this->admin)->get('/admin/games?filter[type]=emparejar')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/games/Index')
            ->has('paginatedGames.data', 1)
        );
});

it('can view create game page', function () {
    actingAs($this->admin)->get('/admin/games/create')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/games/Create')
            ->has('categories')
        );
});

it('can store game of type emparejar', function () {
    $pictograms = Pictogram::factory()->count(4)->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Juego de Pares',
        'type' => 'emparejar',
        'level' => 1,
        'elements' => [
            ['pictogram_id' => $pictograms[0]->id],
            ['pictogram_id' => $pictograms[1]->id],
            ['pictogram_id' => $pictograms[2]->id],
            ['pictogram_id' => $pictograms[3]->id],
        ],
    ])->assertRedirect('/admin/games');

    $this->assertDatabaseHas('games', [
        'name' => 'Juego de Pares',
        'type' => 'emparejar',
    ]);

    $this->assertDatabaseHas('game_elements', [
        'pictogram_id' => $pictograms[0]->id,
        'sequence_order' => null,
    ]);
});

it('can store game of type secuencia with required order', function () {
    $pictograms = Pictogram::factory()->count(2)->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Secuencia Lavado',
        'type' => 'secuencia',
        'level' => 2,
        'elements' => [
            ['pictogram_id' => $pictograms[0]->id, 'sequence_order' => 1],
            ['pictogram_id' => $pictograms[1]->id, 'sequence_order' => 2],
        ],
    ])->assertRedirect('/admin/games');

    $this->assertDatabaseHas('games', [
        'name' => 'Secuencia Lavado',
        'type' => 'secuencia',
    ]);

    $this->assertDatabaseHas('game_elements', [
        'pictogram_id' => $pictograms[0]->id,
        'sequence_order' => 1,
    ]);
});

it('validates sequence_order when type is secuencia', function () {
    $pictogram = Pictogram::factory()->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Bad Secuencia',
        'type' => 'secuencia',
        'level' => 2,
        'elements' => [
            ['pictogram_id' => $pictogram->id], // Missing sequence_order
            ['pictogram_id' => $pictogram->id],
        ],
    ])->assertSessionHasErrors(['elements.0.sequence_order']);
});

it('can store game of type emocion with required text', function () {
    $pictograms = Pictogram::factory()->count(4)->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Juego de Emociones',
        'description' => 'Escucha un ruido fuerte',
        'type' => 'emocion',
        'level' => 3,
        'elements' => [
            ['pictogram_id' => $pictograms[0]->id, 'sequence_order' => 1],
            ['pictogram_id' => $pictograms[1]->id, 'sequence_order' => 2],
            ['pictogram_id' => $pictograms[2]->id, 'sequence_order' => 3],
            ['pictogram_id' => $pictograms[3]->id, 'sequence_order' => 4],
        ],
    ])->assertRedirect('/admin/games');

    $this->assertDatabaseHas('games', [
        'name' => 'Juego de Emociones',
        'description' => 'Escucha un ruido fuerte',
        'type' => 'emocion',
    ]);

    $this->assertDatabaseHas('game_elements', [
        'pictogram_id' => $pictograms[0]->id,
        'sequence_order' => 1,
    ]);
});

it('validates description is required when type is emocion', function () {
    $pictogram = Pictogram::factory()->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Bad Emocion',
        'type' => 'emocion',
        'level' => 2,
        'elements' => [
            ['pictogram_id' => $pictogram->id, 'sequence_order' => 1],
            ['pictogram_id' => $pictogram->id, 'sequence_order' => 2],
            ['pictogram_id' => $pictogram->id, 'sequence_order' => 3],
            ['pictogram_id' => $pictogram->id, 'sequence_order' => 4],
        ],
    ])->assertSessionHasErrors(['description']);
});

it('validates situation_text when type is emocion', function () {
    $pictogram = Pictogram::factory()->create();

    actingAs($this->admin)->post('/admin/games', [
        'name' => 'Bad Emocion',
        'type' => 'emocion',
        'level' => 2,
        'elements' => [
            ['pictogram_id' => $pictogram->id], // Missing sequence_order
            ['pictogram_id' => $pictogram->id],
            ['pictogram_id' => $pictogram->id],
            ['pictogram_id' => $pictogram->id],
        ],
    ])->assertSessionHasErrors(['elements.0.sequence_order']);
});

it('can view edit game page', function () {
    $game = Game::factory()->create();

    actingAs($this->admin)->get("/admin/games/{$game->id}/edit")
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('admin/games/Edit')
            ->has('game')
            ->has('categories')
        );
});

it('can update game and sync elements', function () {
    $game = Game::factory()->create(['type' => 'emparejar']);
    $oldElement = GameElement::factory()->create(['game_id' => $game->id]);
    $newPictogram = Pictogram::factory()->create();

    actingAs($this->admin)->put("/admin/games/{$game->id}", [
        'name' => 'Updated Name',
        'type' => 'emparejar',
        'level' => 5,
        'elements' => [
            ['pictogram_id' => $newPictogram->id],
            ['pictogram_id' => $newPictogram->id],
            ['pictogram_id' => $newPictogram->id],
            ['pictogram_id' => $newPictogram->id],
        ],
    ])->assertRedirect('/admin/games');

    $this->assertDatabaseHas('games', [
        'id' => $game->id,
        'name' => 'Updated Name',
        'level' => 5,
    ]);

    $this->assertDatabaseMissing('game_elements', [
        'id' => $oldElement->id,
    ]);

    $this->assertDatabaseHas('game_elements', [
        'game_id' => $game->id,
        'pictogram_id' => $newPictogram->id,
    ]);
});

it('can delete game', function () {
    $game = Game::factory()->create();

    actingAs($this->admin)->delete("/admin/games/{$game->id}")
        ->assertRedirect('/admin/games');

    $this->assertDatabaseMissing('games', [
        'id' => $game->id,
    ]);
});
