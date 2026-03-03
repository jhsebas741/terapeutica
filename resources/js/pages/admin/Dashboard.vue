<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Panel de Terapeuta" />
        <div class="flex-1 space-y-6 px-4 py-8">
            <h1 class="text-3xl font-bold text-foreground">Vista General</h1>
            <p class="text-muted-foreground">
                Panel administrativo para la gestión terapéutica.
            </p>
        </div>
    </AppLayout>
</template>
