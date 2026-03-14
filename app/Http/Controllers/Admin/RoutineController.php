<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoutineRequest;
use App\Models\Category;
use App\Models\Routine;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoutineController extends Controller
{
    public function index(Request $request)
    {
        $routines = QueryBuilder::for(Routine::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('patient_id', function ($query, $value) {
                    if ($value !== 'all') {
                        $query->where('patient_id', $value);
                    }
                }),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('-created_at')
            ->withCount('routineSteps')
            ->with('patient:id,name')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        $patients = User::role('paciente')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('admin/routines/Index', [
            'paginatedRoutines' => $routines,
            'patients' => $patients,
        ]);
    }

    public function create()
    {
        $patients = User::role('paciente')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = Category::query()
            ->with(['pictograms' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/routines/Create', [
            'patients' => $patients,
            'categories' => $categories,
        ]);
    }

    public function store(RoutineRequest $request)
    {
        $data = $request->validated();

        $routine = Routine::create([
            'patient_id' => $data['patient_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        foreach ($data['steps'] as $index => $step) {
            $routine->routineSteps()->create([
                'pictogram_id' => $step['pictogram_id'],
                'order' => $index + 1,
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rutina creada exitosamente',
        ]);

        return redirect()->route('admin.routines.index');
    }

    public function edit(Routine $routine)
    {
        $routine->load(['routineSteps.pictogram', 'patient:id,name']);

        $patients = User::role('paciente')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = Category::query()
            ->with(['pictograms' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/routines/Edit', [
            'routine' => $routine,
            'patients' => $patients,
            'categories' => $categories,
        ]);
    }

    public function update(RoutineRequest $request, Routine $routine)
    {
        $data = $request->validated();

        $routine->update([
            'patient_id' => $data['patient_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $routine->routineSteps()->delete();

        foreach ($data['steps'] as $index => $step) {
            $routine->routineSteps()->create([
                'pictogram_id' => $step['pictogram_id'],
                'order' => $index + 1,
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rutina actualizada exitosamente',
        ]);

        return redirect()->route('admin.routines.index');
    }

    public function destroy(Routine $routine)
    {
        $routine->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rutina eliminada exitosamente',
        ]);

        return redirect()->route('admin.routines.index');
    }
}
