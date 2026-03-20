<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ChildLayout from '@/layouts/ChildLayout.vue';
</script>

<template>
    <Head title="Mi Panel - Juegos y Rutinas" />
    <ChildLayout>
        <div class="mx-auto flex max-w-4xl flex-col items-center justify-center py-8 sm:py-16">
            <h1 class="mb-10 text-center text-4xl font-extrabold text-sky-800 sm:text-5xl dark:text-sky-200">
                ¡Bienvenido! 👋
            </h1>

            <div class="grid w-full grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8">
                <Link
                    href="/child/routines"
                    class="group flex flex-col items-center rounded-3xl border-4 border-sky-200 bg-white p-8 shadow-lg transition-all hover:scale-105 hover:border-sky-400 hover:shadow-xl sm:p-10 dark:border-sky-800 dark:bg-slate-800 dark:hover:border-sky-600"
                >
                    <span class="mb-4 block text-6xl transition-transform group-hover:scale-110 sm:text-7xl">📅</span>
                    <h2 class="text-2xl font-bold text-sky-700 sm:text-3xl dark:text-sky-300">
                        Mis Rutinas
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Sigue tus actividades paso a paso
                    </p>
                </Link>

                <Link
                    href="/child/games"
                    class="group flex flex-col items-center rounded-3xl border-4 border-amber-200 bg-white p-8 shadow-lg transition-all hover:scale-105 hover:border-amber-400 hover:shadow-xl sm:p-10 dark:border-amber-800 dark:bg-slate-800 dark:hover:border-amber-600"
                >
                    <span class="mb-4 block text-6xl transition-transform group-hover:scale-110 sm:text-7xl">🎮</span>
                    <h2 class="text-2xl font-bold text-amber-700 sm:text-3xl dark:text-amber-300">
                        Jugar
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Diviértete con juegos educativos
                    </p>
                </Link>
            </div>
        </div>
    </ChildLayout>
</template>
