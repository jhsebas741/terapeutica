<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChildGameController extends Controller
{
    public function index(): \Inertia\Response
    {
        $games = Game::query()
            ->where('is_active', true)
            ->with(['gameElements.pictogram'])
            ->withCount('gameElements')
            ->orderBy('name')
            ->get();

        return Inertia::render('patient/games/Index', [
            'games' => $games,
        ]);
    }

    public function show(Game $game): \Inertia\Response
    {
        abort_unless($game->is_active, 404);

        $game->load(['gameElements' => fn ($q) => $q->with('pictogram')->orderBy('sequence_order')]);

        return Inertia::render('patient/games/Show', [
            'game' => $game,
        ]);
    }

    public function complete(Request $request, Game $game): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'result' => ['required', 'in:completado,incompleto,excelente'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'attempts' => ['required', 'integer', 'min:1'],
            'time_spent_sec' => ['nullable', 'integer', 'min:0'],
        ]);

        ProgressLog::create([
            'patient_id' => auth()->id(),
            'activity_type' => 'juego',
            'reference_id' => $game->id,
            'result' => $validated['result'],
            'score' => $validated['score'],
            'attempts' => $validated['attempts'],
            'time_spent_sec' => $validated['time_spent_sec'],
            'completed_at' => now(),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '¡Juego completado! 🎮',
        ]);

        return redirect()->route('patient.games.index');
    }
}
