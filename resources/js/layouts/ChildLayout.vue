<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { HomeIcon, LogOutIcon } from 'lucide-vue-next';
import { onUnmounted } from 'vue';
import { toast } from 'vue-sonner';
import { Toaster } from '@/components/ui/sonner';
import CalmButton from '@/components/CalmButton.vue';

const page = usePage();

const logout = () => {
    router.post('/logout');
};

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
    <div
        class="flex min-h-screen flex-col bg-linear-to-b from-sky-50 to-blue-100 dark:from-slate-900 dark:to-slate-800"
    >
        <!-- Top bar -->
        <header
            class="sticky top-0 z-40 flex items-center justify-between border-b border-sky-200 bg-white/80 px-4 py-3 shadow-sm backdrop-blur-md sm:px-6 dark:border-slate-700 dark:bg-slate-900/80"
        >
            <Link
                href="/child"
                class="flex items-center gap-2 rounded-xl bg-sky-100 px-4 py-2 text-lg font-bold text-sky-700 transition-colors hover:bg-sky-200 dark:bg-sky-900/40 dark:text-sky-300"
            >
                <HomeIcon class="h-5 w-5" />
                <span class="hidden sm:inline">Inicio</span>
            </Link>

            <div class="flex items-center gap-3">
                <span
                    class="text-lg font-semibold text-slate-700 dark:text-slate-200"
                >
                    {{ page.props.auth.user.name }}
                </span>
                <button
                    @click="logout"
                    class="flex items-center gap-1.5 rounded-xl bg-rose-100 px-3 py-2 text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-400"
                >
                    <LogOutIcon class="h-4 w-4" />
                    <span class="hidden sm:inline">Salir</span>
                </button>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <slot />
        </main>

        <CalmButton />
        <Toaster rich-colors position="top-right" />
    </div>
</template>
