<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Game } from '@/types/data/game';

interface Props {
    games: Game[];
}

const { games } = defineProps<Props>();

const getTypeConfig = (type: string) => {
    const configs: Record<
        string,
        { emoji: string; label: string; border: string; bg: string }
    > = {
        emparejar: {
            emoji: '🔗',
            label: 'Emparejar',
            border: 'border-blue-300 hover:border-blue-500 dark:border-blue-700 dark:hover:border-blue-500',
            bg: 'bg-blue-50 dark:bg-blue-950/20',
        },
        secuencia: {
            emoji: '📋',
            label: 'Secuencia',
            border: 'border-amber-300 hover:border-amber-500 dark:border-amber-700 dark:hover:border-amber-500',
            bg: 'bg-amber-50 dark:bg-amber-950/20',
        },
        emocion: {
            emoji: '❤️',
            label: 'Emociones',
            border: 'border-rose-300 hover:border-rose-500 dark:border-rose-700 dark:hover:border-rose-500',
            bg: 'bg-rose-50 dark:bg-rose-950/20',
        },
    };
    return (
        configs[type] || {
            emoji: '🎮',
            label: type,
            border: 'border-slate-300',
            bg: 'bg-slate-50',
        }
    );
};
</script>

<template>
    <Head title="Juegos" />
    <ChildLayout>
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/child"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 transition hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>
                <h1
                    class="text-3xl font-extrabold text-amber-800 sm:text-4xl dark:text-amber-200"
                >
                    🎮 Juegos
                </h1>
            </div>

            <div
                v-if="games.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-md dark:bg-slate-800"
            >
                <span class="mb-4 text-6xl">🎲</span>
                <p
                    class="text-xl font-semibold text-slate-600 dark:text-slate-300"
                >
                    No hay juegos disponibles todavía
                </p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="game in games"
                    :key="game.id"
                    :href="`/child/games/${game.id}`"
                    class="group flex flex-col items-center rounded-2xl border-3 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-lg sm:p-6 dark:bg-slate-800"
                    :class="getTypeConfig(game.type).border"
                >
                    <!-- Type icon -->
                    <div
                        class="mb-3 flex h-16 w-16 items-center justify-center rounded-2xl"
                        :class="getTypeConfig(game.type).bg"
                    >
                        <span class="text-3xl">{{
                            getTypeConfig(game.type).emoji
                        }}</span>
                    </div>

                    <h3
                        class="mb-1 text-center text-lg font-bold text-slate-800 dark:text-slate-100"
                    >
                        {{ game.name }}
                    </h3>

                    <span
                        class="mb-2 rounded-full bg-slate-100 px-3 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-700 dark:text-slate-400"
                    >
                        {{ getTypeConfig(game.type).label }} · Nivel
                        {{ game.level }}
                    </span>

                    <!-- Pictogram preview -->
                    <div
                        v-if="game.game_elements?.length"
                        class="mt-2 flex gap-1.5"
                    >
                        <div
                            v-for="element in game.game_elements.slice(0, 4)"
                            :key="element.id"
                            class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-lg border bg-white shadow-sm dark:border-slate-600 dark:bg-slate-700"
                        >
                            <img
                                v-if="element.pictogram?.image_url"
                                :src="`/storage/${element.pictogram.image_url}`"
                                :alt="element.pictogram?.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else class="text-sm">{{
                                element.pictogram?.icon_text
                            }}</span>
                        </div>
                    </div>

                    <span
                        class="mt-3 text-lg transition-transform group-hover:translate-x-1"
                    >
                        ▶️
                    </span>
                </Link>
            </div>
        </div>
    </ChildLayout>
</template>
