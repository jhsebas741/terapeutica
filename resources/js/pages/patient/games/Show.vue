<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Game } from '@/types/data/game';
import EmocionGame from './play/EmocionGame.vue';
import EmparejarGame from './play/EmparejarGame.vue';
import SecuenciaGame from './play/SecuenciaGame.vue';

interface Props {
    game: Game;
}

const { game } = defineProps<Props>();

const goBack = () => {
    router.visit('/child/games');
};
</script>

<template>
    <Head :title="game.name" />
    <ChildLayout>
        <div class="mx-auto max-w-3xl">
            <div class="mb-6 flex items-center gap-3">
                <button
                    @click="goBack"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 transition hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </button>
                <h1
                    class="text-2xl font-extrabold text-amber-800 sm:text-3xl dark:text-amber-200"
                >
                    {{ game.name }}
                </h1>
            </div>

            <EmparejarGame v-if="game.type === 'emparejar'" :game="game" />
            <SecuenciaGame v-else-if="game.type === 'secuencia'" :game="game" />
            <EmocionGame v-else-if="game.type === 'emocion'" :game="game" />
        </div>
    </ChildLayout>
</template>
