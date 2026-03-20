<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameRequest;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $games = QueryBuilder::for(Game::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('level'),
            ])
            ->allowedSorts(['name', 'created_at', 'level'])
            ->defaultSort('-created_at')
            ->withCount('gameElements')
            ->with(['gameElements' => fn ($q) => $q->with('pictogram')->orderBy('sequence_order')->limit(4)])
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('admin/games/Index', [
            'paginatedGames' => $games,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::query()
            ->with(['pictograms' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/games/Create', [
            'categories' => $categories,
            'defaultType' => $request->query('type', 'emparejar'),
        ]);
    }

    public function store(GameRequest $request)
    {
        $data = $request->validated();

        $game = Game::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'level' => $data['level'],
            'is_active' => true,
        ]);

        foreach ($data['elements'] as $element) {
            $game->gameElements()->create([
                'pictogram_id' => $element['pictogram_id'],
                'sequence_order' => in_array($data['type'], ['secuencia', 'emocion']) ? $element['sequence_order'] : null,
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Juego creado exitosamente',
        ]);

        return redirect()->route('admin.games.index');
    }

    public function edit(Game $game)
    {
        $game->load(['gameElements.pictogram']);

        $categories = Category::query()
            ->with(['pictograms' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/games/Edit', [
            'game' => $game,
            'categories' => $categories,
        ]);
    }

    public function update(GameRequest $request, Game $game)
    {
        $data = $request->validated();

        $game->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'level' => $data['level'],
            // Notice: We do not allow changing the 'type' of an existing game,
            // as it would invalidate existing elements logically.
            // However, the rule allows it through the form.
            // If they modify the type, we recreate elements regardless.
        ]);

        if (isset($data['type'])) {
            $game->update(['type' => $data['type']]);
        }

        $game->gameElements()->delete();

        foreach ($data['elements'] as $element) {
            $game->gameElements()->create([
                'pictogram_id' => $element['pictogram_id'],
                'sequence_order' => in_array($data['type'], ['secuencia', 'emocion']) ? $element['sequence_order'] : null,
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Juego actualizado exitosamente',
        ]);

        return redirect()->route('admin.games.index');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Juego eliminado exitosamente',
        ]);

        return redirect()->route('admin.games.index');
    }
}
