<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = QueryBuilder::for(User::role('paciente'))
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    });
                }),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('name')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        return Inertia::render('admin/patients/Index', [
            'paginatedPatients' => $patients,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/patients/Create');
    }

    public function store(PatientRequest $request)
    {
        $data = $request->validated();

        $patient = User::create($data);
        $patient->assignRole('paciente');

        $patient->patientSetting()->create();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paciente creado exitosamente',
        ]);

        return redirect()->route('admin.patients.index');
    }

    public function edit(User $patient)
    {
        $patient->load('patientSetting');

        return Inertia::render('admin/patients/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(PatientRequest $request, User $patient)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $patient->update($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paciente actualizado exitosamente',
        ]);

        return redirect()->route('admin.patients.index');
    }

    public function toggleActive(User $patient)
    {
        $patient->update(['is_active' => ! $patient->is_active]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $patient->is_active ? 'Paciente activado' : 'Paciente desactivado',
        ]);

        return redirect()->route('admin.patients.index');
    }
}
