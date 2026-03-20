<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Routine } from '@/types/data/routine';

interface Props {
    routines: Routine[];
}

const { routines } = defineProps<Props>();
</script>

<template>
    <Head title="Mis Rutinas" />
    <ChildLayout>
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/child"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h1 class="text-3xl font-extrabold text-sky-800 sm:text-4xl dark:text-sky-200">
                    📅 Mis Rutinas
                </h1>
            </div>

            <div
                v-if="routines.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-md dark:bg-slate-800"
            >
                <span class="mb-4 text-6xl">😴</span>
                <p class="text-xl font-semibold text-slate-600 dark:text-slate-300">
                    No tienes rutinas asignadas todavía
                </p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2">
                <Link
                    v-for="routine in routines"
                    :key="routine.id"
                    :href="`/child/routines/${routine.id}`"
                    class="group flex items-center gap-4 rounded-2xl border-2 border-sky-200 bg-white p-4 shadow-md transition-all hover:scale-[1.02] hover:border-sky-400 hover:shadow-lg sm:p-5 dark:border-sky-800 dark:bg-slate-800 dark:hover:border-sky-600"
                >
                    <!-- Pictogram previews -->
                    <div class="flex -space-x-2">
                        <div
                            v-for="(step, idx) in routine.routine_steps?.slice(0, 3)"
                            :key="idx"
                            class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl border-2 border-white bg-sky-50 shadow-sm dark:border-slate-700 dark:bg-sky-900/20"
                        >
                            <img
                                v-if="step.pictogram?.image_url"
                                :src="`/storage/${step.pictogram.image_url}`"
                                :alt="step.pictogram?.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else class="text-lg">{{
                                step.pictogram?.icon_text
                            }}</span>
                        </div>
                        <div
                            v-if="(routine.routine_steps_count ?? 0) > 3"
                            class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-white bg-sky-100 text-xs font-bold text-sky-600 shadow-sm dark:border-slate-700 dark:bg-sky-900/40"
                        >
                            +{{ (routine.routine_steps_count ?? 0) - 3 }}
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-lg font-bold text-slate-800 dark:text-slate-100">
                            {{ routine.name }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ routine.routine_steps_count ?? 0 }} pasos
                        </p>
                    </div>

                    <span class="text-2xl transition-transform group-hover:translate-x-1">
                        ▶️
                    </span>
                </Link>
            </div>
        </div>
    </ChildLayout>
</template>
