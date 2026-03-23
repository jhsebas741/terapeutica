<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeftIcon, DicesIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Game } from '@/types/data/game';

interface Props {
    games: Game[];
}

const { games } = defineProps<Props>();

const typeConfigs = {
    emparejar: {
        emoji: '🔗',
        label: 'Emparejar',
        sublabel: 'Imagen ↔ Texto',
        border: 'border-blue-300 hover:border-blue-500 dark:border-blue-700 dark:hover:border-blue-500',
        bg: 'bg-blue-50 dark:bg-blue-950/20',
        quickBg: 'bg-blue-500 hover:bg-blue-600',
    },
    secuencia: {
        emoji: '📋',
        label: 'Secuencia',
        sublabel: 'Ordena los pasos',
        border: 'border-amber-300 hover:border-amber-500 dark:border-amber-700 dark:hover:border-amber-500',
        bg: 'bg-amber-50 dark:bg-amber-950/20',
        quickBg: 'bg-amber-500 hover:bg-amber-600',
    },
    emocion: {
        emoji: '❤️',
        label: 'Emociones',
        sublabel: 'Identifica la emoción',
        border: 'border-rose-300 hover:border-rose-500 dark:border-rose-700 dark:hover:border-rose-500',
        bg: 'bg-rose-50 dark:bg-rose-950/20',
        quickBg: 'bg-rose-500 hover:bg-rose-600',
    },
} as const;

type GameType = keyof typeof typeConfigs;

const getTypeConfig = (type: string) => {
    return (
        typeConfigs[type as GameType] || {
            emoji: '🎮',
            label: type,
            sublabel: '',
            border: 'border-slate-300',
            bg: 'bg-slate-50',
            quickBg: 'bg-slate-500',
        }
    );
};

const gameTypes: GameType[] = ['emparejar', 'secuencia', 'emocion'];

const groupedGames = computed(() => {
    const groups: Record<string, Game[]> = {};
    for (const type of gameTypes) {
        const filtered = games.filter((g) => g.type === type);
        if (filtered.length > 0) {
            groups[type] = filtered;
        }
    }
    return groups;
});

const hasGames = computed(() => games.length > 0);
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

            <!-- Quick play buttons -->
            <div class="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <Link
                    v-for="type in gameTypes"
                    :key="type"
                    :href="`/child/games/quick/${type}`"
                    class="group flex flex-col items-center rounded-2xl p-4 text-white shadow-md transition-all hover:scale-105 hover:shadow-lg sm:p-5"
                    :class="typeConfigs[type].quickBg"
                >
                    <DicesIcon class="mb-1 h-5 w-5 opacity-70" />
                    <span class="text-2xl sm:text-3xl">{{
                        typeConfigs[type].emoji
                    }}</span>
                    <span class="mt-1 text-xs font-bold sm:text-sm">
                        {{ typeConfigs[type].label }}
                    </span>
                    <span class="text-[10px] font-medium opacity-80 sm:text-xs">
                        Juego rápido
                    </span>
                </Link>
            </div>

            <!-- Empty state -->
            <div
                v-if="!hasGames"
                class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-md dark:bg-slate-800"
            >
                <span class="mb-4 text-6xl">🎲</span>
                <p
                    class="text-xl font-semibold text-slate-600 dark:text-slate-300"
                >
                    No hay juegos disponibles todavía
                </p>
            </div>

            <!-- Grouped games -->
            <div v-else id="all-games" class="space-y-8">
                <section v-for="(typeGames, type) in groupedGames" :key="type">
                    <!-- Group header -->
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-2xl">{{
                            getTypeConfig(type).emoji
                        }}</span>
                        <h2
                            class="text-xl font-bold text-slate-800 dark:text-slate-100"
                        >
                            {{ getTypeConfig(type).label }}
                        </h2>
                        <span
                            class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-700 dark:text-slate-400"
                        >
                            {{ typeGames.length }}
                        </span>
                    </div>

                    <!-- Games grid -->
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="game in typeGames"
                            :key="game.id"
                            :href="`/child/games/${game.id}`"
                            class="group flex items-center gap-3 rounded-2xl border-2 bg-white p-3 shadow-sm transition-all hover:scale-[1.02] hover:shadow-md sm:p-4 dark:bg-slate-800"
                            :class="getTypeConfig(type).border"
                        >
                            <!-- Pictogram preview -->
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl sm:h-14 sm:w-14"
                                :class="getTypeConfig(type).bg"
                            >
                                <img
                                    v-if="
                                        game.game_elements?.[0]?.pictogram
                                            ?.image_url
                                    "
                                    :src="`/storage/${game.game_elements[0].pictogram.image_url}`"
                                    :alt="game.game_elements[0].pictogram?.name"
                                    class="h-full w-full object-cover"
                                />
                                <span v-else class="text-2xl">{{
                                    getTypeConfig(type).emoji
                                }}</span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate text-sm font-bold text-slate-800 sm:text-base dark:text-slate-100"
                                >
                                    {{ game.name }}
                                </h3>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Nivel {{ game.level }}
                                </p>
                            </div>

                            <span
                                class="text-lg transition-transform group-hover:translate-x-1"
                            >
                                ▶️
                            </span>
                        </Link>
                    </div>
                </section>
            </div>
        </div>
    </ChildLayout>
</template>
