<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\ProgressLog;
use App\Models\Routine;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(): \Inertia\Response
    {
        $activePatients = User::role('paciente')->where('is_active', true)->count();
        $totalPatients = User::role('paciente')->count();
        $totalRoutines = Routine::where('is_active', true)->count();
        $totalGames = Game::where('is_active', true)->count();

        $recentActivities = ProgressLog::query()
            ->with('patient:id,name')
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get()
            ->map(function (ProgressLog $log) {
                $referenceName = null;

                if ($log->activity_type === 'rutina') {
                    $referenceName = Routine::find($log->reference_id)?->name;
                } elseif ($log->activity_type === 'juego') {
                    $referenceName = Game::find($log->reference_id)?->name;
                }

                return [
                    'id' => $log->id,
                    'patient_name' => $log->patient?->name ?? 'Desconocido',
                    'activity_type' => $log->activity_type,
                    'reference_name' => $referenceName ?? 'Actividad',
                    'result' => $log->result,
                    'score' => $log->score,
                    'attempts' => $log->attempts,
                    'time_spent_sec' => $log->time_spent_sec,
                    'completed_at' => $log->completed_at?->diffForHumans(),
                    'completed_at_date' => $log->completed_at?->format('d/m/Y H:i'),
                ];
            });

        $todayCount = ProgressLog::whereDate('completed_at', today())->count();

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'activePatients' => $activePatients,
                'totalPatients' => $totalPatients,
                'totalRoutines' => $totalRoutines,
                'totalGames' => $totalGames,
                'todayActivities' => $todayCount,
            ],
            'recentActivities' => $recentActivities,
        ]);
    }
}
