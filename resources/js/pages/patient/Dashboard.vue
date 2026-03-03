<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <Head title="Mi Panel - Juegos y Rutinas" />
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-background p-6"
    >
        <h1 class="mb-8 text-4xl font-extrabold text-primary">
            ¡Hola, {{ page.props.auth.user.name }}!
        </h1>
        <div class="grid w-full max-w-4xl grid-cols-1 gap-8 md:grid-cols-2">
            <div
                class="cursor-pointer rounded-3xl border-4 border-transparent bg-card p-8 text-center shadow-xl transition-transform hover:scale-105 hover:border-sidebar-primary"
            >
                <span class="mb-4 block text-6xl">📅</span>
                <h2 class="text-3xl font-bold text-card-foreground">
                    Mis Rutinas
                </h2>
            </div>
            <div
                class="cursor-pointer rounded-3xl border-4 border-transparent bg-card p-8 text-center shadow-xl transition-transform hover:scale-105 hover:border-sidebar-primary"
            >
                <span class="mb-4 block text-6xl">🎮</span>
                <h2 class="text-3xl font-bold text-card-foreground">Jugar</h2>
            </div>
        </div>
    </div>
</template>
