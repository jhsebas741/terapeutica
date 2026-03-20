<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use App\Models\Routine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChildRoutineController extends Controller
{
    public function index(): \Inertia\Response
    {
        $routines = Routine::query()
            ->where('patient_id', auth()->id())
            ->where('is_active', true)
            ->with(['routineSteps.pictogram'])
            ->withCount('routineSteps')
            ->orderBy('name')
            ->get();

        return Inertia::render('patient/routines/Index', [
            'routines' => $routines,
        ]);
    }

    public function show(Routine $routine): \Inertia\Response
    {
        abort_unless($routine->patient_id === auth()->id(), 403);

        $routine->load(['routineSteps.pictogram']);

        return Inertia::render('patient/routines/Show', [
            'routine' => $routine,
        ]);
    }

    public function complete(Request $request, Routine $routine): \Illuminate\Http\RedirectResponse
    {
        abort_unless($routine->patient_id === auth()->id(), 403);

        ProgressLog::create([
            'patient_id' => auth()->id(),
            'activity_type' => 'rutina',
            'reference_id' => $routine->id,
            'result' => 'completado',
            'score' => 100,
            'attempts' => 1,
            'time_spent_sec' => $request->input('time_spent_sec'),
            'completed_at' => now(),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '¡Rutina completada! 🎉',
        ]);

        return redirect()->route('patient.routines.index');
    }
}
