<?php

use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PatientSettingController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\SetupController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(auth()->user()->hasRole('admin') ? '/admin' : '/child');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::inertia('/', 'admin/Dashboard')->name('admin.dashboard');

        // Patients
        Route::patch('patients/{patient}/toggle-active', [PatientController::class, 'toggleActive'])->name('admin.patients.toggle-active');
        Route::resource('patients', PatientController::class)->except(['show', 'destroy'])->names('admin.patients');
        
        // Patient Settings
        Route::get('patients/{patient}/settings', [PatientSettingController::class, 'edit'])->name('admin.patients.settings.edit');
        Route::put('patients/{patient}/settings', [PatientSettingController::class, 'update'])->name('admin.patients.settings.update');
    });

    // Patient Routes
    Route::middleware(['role:paciente'])->prefix('child')->group(function () {
        Route::inertia('/', 'patient/Dashboard')->name('patient.dashboard');
    });
});

require __DIR__.'/settings.php';
