<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ActivityIcon,
    CalendarIcon,
    Gamepad2Icon,
    ListChecksIcon,
    UsersIcon,
} from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

interface Activity {
    id: number;
    patient_name: string;
    activity_type: string;
    reference_name: string;
    result: string;
    score: number;
    attempts: number;
    time_spent_sec: number | null;
    completed_at: string;
    completed_at_date: string;
}

interface Stats {
    activePatients: number;
    totalPatients: number;
    totalRoutines: number;
    totalGames: number;
    todayActivities: number;
}

interface Props {
    stats: Stats;
    recentActivities: Activity[];
}

const { stats, recentActivities } = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const getResultBadge = (result: string) => {
    const map: Record<string, { label: string; class: string }> = {
        excelente: {
            label: '⭐ Excelente',
            class: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
        },
        completado: {
            label: '✅ Completado',
            class: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        },
        incompleto: {
            label: '⏳ Incompleto',
            class: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        },
    };
    return map[result] || { label: result, class: 'bg-slate-100 text-slate-600' };
};

const getActivityIcon = (type: string) => {
    return type === 'rutina' ? '📋' : '🎮';
};

const formatTime = (seconds: number | null) => {
    if (!seconds) return '-';
    if (seconds < 60) return `${seconds}s`;
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
};
</script>

<template>
    <Head title="Panel de Terapeuta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 px-4 py-8">
            <div>
                <h1 class="text-3xl font-bold text-foreground">
                    Vista General
                </h1>
                <p class="text-muted-foreground">
                    Panel administrativo para la gestión terapéutica.
                </p>
            </div>

            <!-- Stats cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    class="rounded-xl border bg-card p-5 shadow-sm"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">
                            Pacientes Activos
                        </span>
                        <UsersIcon class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-3xl font-bold text-foreground">
                        {{ stats.activePatients }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        de {{ stats.totalPatients }} registrados
                    </p>
                </div>

                <div
                    class="rounded-xl border bg-card p-5 shadow-sm"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">
                            Rutinas Activas
                        </span>
                        <ListChecksIcon class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-3xl font-bold text-foreground">
                        {{ stats.totalRoutines }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        asignadas a pacientes
                    </p>
                </div>

                <div
                    class="rounded-xl border bg-card p-5 shadow-sm"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">
                            Juegos Activos
                        </span>
                        <Gamepad2Icon class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-3xl font-bold text-foreground">
                        {{ stats.totalGames }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        disponibles para jugar
                    </p>
                </div>

                <div
                    class="rounded-xl border bg-card p-5 shadow-sm"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">
                            Actividades Hoy
                        </span>
                        <CalendarIcon class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-3xl font-bold text-foreground">
                        {{ stats.todayActivities }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        rutinas y juegos completados
                    </p>
                </div>
            </div>

            <!-- Recent activities -->
            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center gap-2 border-b px-5 py-4">
                    <ActivityIcon class="h-5 w-5 text-muted-foreground" />
                    <h2 class="text-lg font-semibold text-foreground">
                        Actividades Recientes
                    </h2>
                </div>

                <div
                    v-if="recentActivities.length === 0"
                    class="p-8 text-center text-muted-foreground"
                >
                    <span class="mb-2 block text-4xl">📊</span>
                    <p>No hay actividades registradas todavía.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="px-5 py-3 font-medium">Paciente</th>
                                <th class="px-5 py-3 font-medium">Actividad</th>
                                <th class="px-5 py-3 font-medium">Resultado</th>
                                <th class="px-5 py-3 font-medium text-center">Puntaje</th>
                                <th class="px-5 py-3 font-medium text-center">Intentos</th>
                                <th class="px-5 py-3 font-medium text-center">Tiempo</th>
                                <th class="px-5 py-3 font-medium">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="border-b last:border-0 hover:bg-muted/50"
                            >
                                <td class="px-5 py-3 font-medium text-foreground">
                                    {{ activity.patient_name }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <span>{{ getActivityIcon(activity.activity_type) }}</span>
                                        <div>
                                            <p class="font-medium text-foreground">
                                                {{ activity.reference_name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ activity.activity_type === 'rutina' ? 'Rutina' : 'Juego' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="getResultBadge(activity.result).class"
                                    >
                                        {{ getResultBadge(activity.result).label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center font-semibold text-foreground">
                                    {{ activity.score }}
                                </td>
                                <td class="px-5 py-3 text-center text-muted-foreground">
                                    {{ activity.attempts }}
                                </td>
                                <td class="px-5 py-3 text-center text-muted-foreground">
                                    {{ formatTime(activity.time_spent_sec) }}
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-foreground">
                                        {{ activity.completed_at }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ activity.completed_at_date }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
