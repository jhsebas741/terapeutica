<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onUnmounted } from 'vue';
import { toast } from 'vue-sonner';
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

onUnmounted(
    router.on('flash', (event) => {
        switch (event.detail.flash.toast?.type) {
            case 'success':
                toast.success(event.detail.flash.toast.message);
                break;
            case 'error':
                toast.error(event.detail.flash.toast.message);
                break;
            case 'warning':
                toast.warning(event.detail.flash.toast.message);
                break;
            case 'info':
                toast.info(event.detail.flash.toast.message);
                break;
        }
    }),
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster rich-colors position="top-right" />
</template>
