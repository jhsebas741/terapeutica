<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PatientSettingController;
use App\Http\Controllers\Admin\PictogramController;
use App\Http\Controllers\Admin\RoutineController;
use App\Http\Controllers\Child\ChildGameController;
use App\Http\Controllers\Child\ChildRoutineController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(auth()->user()->hasRole('admin') ? '/admin' : '/child');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/', DashboardController::class)->name('admin.dashboard');

        // Patients
        Route::patch('patients/{patient}/toggle-active', [PatientController::class, 'toggleActive'])->name('admin.patients.toggle-active');
        Route::resource('patients', PatientController::class)->except(['show', 'destroy'])->names('admin.patients');

        // Patient Settings
        Route::get('patients/{patient}/settings', [PatientSettingController::class, 'edit'])->name('admin.patients.settings.edit');
        Route::put('patients/{patient}/settings', [PatientSettingController::class, 'update'])->name('admin.patients.settings.update');

        // Catalog
        Route::resource('categories', CategoryController::class)->except(['show'])->names('admin.categories');
        Route::resource('pictograms', PictogramController::class)->except(['show'])->names('admin.pictograms');

        // Routines
        Route::resource('routines', RoutineController::class)->except(['show'])->names('admin.routines');

        // Games
        Route::resource('games', GameController::class)->except(['show'])->names('admin.games');
    });

    // Patient Routes
    Route::middleware(['role:paciente'])->prefix('child')->group(function () {
        Route::inertia('/', 'patient/Dashboard')->name('patient.dashboard');

        // Routines
        Route::get('routines', [ChildRoutineController::class, 'index'])->name('patient.routines.index');
        Route::get('routines/{routine}', [ChildRoutineController::class, 'show'])->name('patient.routines.show');
        Route::post('routines/{routine}/complete', [ChildRoutineController::class, 'complete'])->name('patient.routines.complete');

        // Games
        Route::get('games', [ChildGameController::class, 'index'])->name('patient.games.index');
        Route::get('games/quick/{type}', [ChildGameController::class, 'quickPlay'])->name('patient.games.quick');
        Route::get('games/{game}', [ChildGameController::class, 'show'])->name('patient.games.show');
        Route::post('games/{game}/complete', [ChildGameController::class, 'complete'])->name('patient.games.complete');
    });
});

require __DIR__.'/settings.php';
