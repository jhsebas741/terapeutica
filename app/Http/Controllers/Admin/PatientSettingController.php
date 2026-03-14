<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientSettingController extends Controller
{
    public function edit(User $patient)
    {
        $patient->load('patientSetting');

        return Inertia::render('admin/patients/settings/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, User $patient)
    {
        $validated = $request->validate([
            'tts_enabled' => ['required', 'boolean'],
            'smooth_animations' => ['required', 'boolean'],
            'stimulation_level' => ['required', 'in:low,medium,high'],
        ]);

        $patient->patientSetting()->update($validated);

        return redirect()->route('admin.patients.index')->with('toast', [
            'type' => 'success',
            'message' => 'Configuración sensorial actualizada.',
        ]);
    }
}
